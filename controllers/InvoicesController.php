<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Tiers.php';

class InvoicesController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 50;
        $offset = ($page - 1) * $limit;

        $params = [];
        $where  = '';
        if ($search !== '') {
            $where    = "WHERE (t.name LIKE ? OR i.ref LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $stmt = $this->pdo->prepare(
            "SELECT i.*, COALESCE(t.name, '—') AS tiers_name
             FROM invoices i
             LEFT JOIN tiers t ON t.id = i.tiers_id
             $where
             ORDER BY i.date_invoice DESC
             LIMIT $limit OFFSET $offset"
        );
        $stmt->execute($params);
        $invoices = $stmt->fetchAll();

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM invoices i LEFT JOIN tiers t ON t.id = i.tiers_id $where");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = max(1, (int)ceil($total / $limit));

        $tiersAll = (new Tiers())->findAll([], 'name ASC');
        $user     = $_SESSION['user'];
        require_once __DIR__ . '/../views/invoices.php';
    }

    public function store(): void
    {
        $tiersId     = (int)($_POST['tiers_id'] ?? 0) ?: null;
        $ref         = trim($_POST['ref'] ?? '');
        $dateInvoice = $_POST['date_invoice'] ?? '';
        $dateDue     = $_POST['date_due'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $totalHt     = (float)str_replace(',', '.', $_POST['total_ht'] ?? '0');
        $totalTtc    = (float)str_replace(',', '.', $_POST['total_ttc'] ?? '0');
        $status      = (int)($_POST['status'] ?? 2);

        if ($totalHt <= 0 || $dateInvoice === '') {
            header('Location: ' . APP_URL . '/invoices?error=' . urlencode('Montant HT et date de facture obligatoires.'));
            exit;
        }

        if (!in_array($status, [0, 1, 2, 3], true)) {
            $status = 2;
        }

        // Générer une ref unique si vide
        if ($ref === '') {
            $ref = 'MAN-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
        }

        $dateDue  = $dateDue  !== '' ? $dateDue  : null;
        $datePaid = $status === 2     ? $dateInvoice : null;
        $isOverdue = ($status !== 2 && $dateDue !== null && $dateDue < date('Y-m-d')) ? 1 : 0;
        $totalTtc = $totalTtc > 0 ? $totalTtc : round($totalHt * 1.20, 2);

        $stmt = $this->pdo->prepare(
            "INSERT INTO invoices (tiers_id, ref, date_invoice, date_due, date_paid, total_ht, total_ttc, status, is_overdue)
             VALUES (:tiers_id, :ref, :date_invoice, :date_due, :date_paid, :total_ht, :total_ttc, :status, :is_overdue)"
        );
        $stmt->execute([
            'tiers_id'     => $tiersId,
            'ref'          => $ref,
            'date_invoice' => $dateInvoice,
            'date_due'     => $dateDue,
            'date_paid'    => $datePaid,
            'total_ht'     => $totalHt,
            'total_ttc'    => $totalTtc,
            'status'       => $status,
            'is_overdue'   => $isOverdue,
        ]);
        $invoiceId = (int)$this->pdo->lastInsertId();

        // Ajouter une ligne de facture avec description si fournie
        if ($description !== '' && $invoiceId > 0) {
            $this->pdo->prepare(
                "INSERT INTO invoice_lines (invoice_id, description, qty, unit_price, total_ht)
                 VALUES (?, ?, 1, ?, ?)"
            )->execute([$invoiceId, $description, $totalHt, $totalHt]);
        }

        // Créer le paiement si facture marquée payée
        if ($status === 2) {
            $this->pdo->prepare(
                "INSERT INTO payments (invoice_id, tiers_id, amount, date_payment, method, method_label)
                 VALUES (?, ?, ?, ?, 'inconnu', 'Saisie manuelle')"
            )->execute([$invoiceId, $tiersId, $totalHt, $dateInvoice]);
        }

        header('Location: ' . APP_URL . '/invoices?message=' . urlencode('Facture ajoutée.'));
        exit;
    }

    public function destroy(int $id): void
    {
        // Supprimer les paiements liés puis la facture
        $this->pdo->prepare("DELETE FROM payments WHERE invoice_id = ?")->execute([$id]);
        $this->pdo->prepare("DELETE FROM invoices WHERE id = ?")->execute([$id]);
        header('Location: ' . APP_URL . '/invoices?message=' . urlencode('Facture supprimée.'));
        exit;
    }

    public function markPaid(int $id): void
    {
        $invoice = $this->pdo->prepare("SELECT * FROM invoices WHERE id = ?");
        $invoice->execute([$id]);
        $row = $invoice->fetch();
        if (!$row) {
            header('Location: ' . APP_URL . '/invoices?error=' . urlencode('Facture introuvable.'));
            exit;
        }

        $today = date('Y-m-d');
        $this->pdo->prepare(
            "UPDATE invoices SET status=2, date_paid=?, is_overdue=0 WHERE id=?"
        )->execute([$today, $id]);

        // Créer un paiement s'il n'en existe pas déjà
        $exists = (int)$this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE invoice_id=?")->execute([$id]);
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM payments WHERE invoice_id=?");
        $countStmt->execute([$id]);
        if ((int)$countStmt->fetchColumn() === 0) {
            $this->pdo->prepare(
                "INSERT INTO payments (invoice_id, tiers_id, amount, date_payment, method, method_label)
                 VALUES (?, ?, ?, ?, 'inconnu', 'Saisie manuelle')"
            )->execute([$id, $row['tiers_id'], $row['total_ht'], $today]);
        }

        header('Location: ' . APP_URL . '/invoices?message=' . urlencode('Facture marquée payée.'));
        exit;
    }
}
