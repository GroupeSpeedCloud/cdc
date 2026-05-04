<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Tiers.php';
require_once __DIR__ . '/../services/PaymentAnalyzerService.php';

class PaymentsController
{
    public function index(): void
    {
        $paymentModel = new Payment();
        $analyzer     = new PaymentAnalyzerService();

        $methodsBreakdown   = $paymentModel->getMethodsBreakdown();
        $frequencyDist      = $analyzer->getFrequencyDistribution();
        $recentPayments     = $paymentModel->getRecentPayments(50);
        $monthlyTotals      = $paymentModel->getMonthlyTotals(12);
        $totalCollected     = $paymentModel->getTotalCollected();
        $tiersAll           = (new Tiers())->findAll([], 'name ASC');
        $user               = $_SESSION['user'];

        require_once __DIR__ . '/../views/payments.php';
    }

    public function store(): void
    {
        $tiersId     = (int)($_POST['tiers_id'] ?? 0) ?: null;
        $invoiceId   = (int)($_POST['invoice_id'] ?? 0) ?: null;
        $amount      = (float)str_replace(',', '.', $_POST['amount'] ?? '0');
        $datePay     = trim($_POST['date_payment'] ?? '');
        $method      = $_POST['method'] ?? 'inconnu';
        $methodLabel = trim($_POST['method_label'] ?? 'Saisie manuelle');

        if ($amount <= 0 || $datePay === '') {
            header('Location: ' . APP_URL . '/payments?error=' . urlencode('Montant et date obligatoires.'));
            exit;
        }

        $allowedMethods = ['CB', 'virement', 'chèque', 'espèces', 'inconnu'];
        if (!in_array($method, $allowedMethods, true)) {
            $method = 'inconnu';
        }

        $pdo = getDB();

        // Si une facture est liée, récupérer le tiers automatiquement
        if ($invoiceId !== null && $tiersId === null) {
            $row = $pdo->prepare("SELECT tiers_id FROM invoices WHERE id = ?");
            $row->execute([$invoiceId]);
            $tiersId = $row->fetchColumn() ?: null;
        }

        $pdo->prepare(
            "INSERT INTO payments (invoice_id, tiers_id, amount, date_payment, method, method_label)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([$invoiceId, $tiersId, $amount, $datePay, $method, $methodLabel ?: 'Saisie manuelle']);

        // Marquer la facture comme payée si elle ne l'est pas encore
        if ($invoiceId !== null) {
            $pdo->prepare(
                "UPDATE invoices SET status=2, date_paid=?, is_overdue=0 WHERE id=? AND status != 2"
            )->execute([$datePay, $invoiceId]);
        }

        header('Location: ' . APP_URL . '/payments?message=' . urlencode('Paiement enregistré.'));
        exit;
    }

    public function destroy(int $id): void
    {
        $pdo = getDB();
        $pdo->prepare("DELETE FROM payments WHERE id = ?")->execute([$id]);
        header('Location: ' . APP_URL . '/payments?message=' . urlencode('Paiement supprimé.'));
        exit;
    }
}
