<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Subscription.php';
require_once __DIR__ . '/../models/Tiers.php';
require_once __DIR__ . '/../models/Product.php';

class SubscriptionsController
{
    private Subscription $model;

    private function subscriptionsTableExists(): bool
    {
        try {
            $stmt = getDB()->prepare('SHOW TABLES LIKE ?');
            $stmt->execute(['subscriptions']);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function __construct()
    {
        $this->model = new Subscription();
    }

    public function index(): void
    {
        $search    = trim($_GET['search'] ?? '');
        $recFilter = in_array($_GET['recurrence'] ?? '', ['monthly','quarterly','annual','one_time']) ? $_GET['recurrence'] : '';
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $limit     = 40;
        $offset    = ($page - 1) * $limit;

        if ($this->subscriptionsTableExists()) {
            $subscriptions = $this->model->getAll($limit, $offset, $search, $recFilter);
            $total         = $this->model->countAll($search, $recFilter);
            $pages         = max(1, (int)ceil($total / $limit));

            $mrr = $this->model->getMRR();
            $arr = $this->model->getARR();
        } else {
            $subscriptions = [];
            $total         = 0;
            $pages         = 1;
            $mrr           = 0.0;
            $arr           = 0.0;
            $_GET['error'] = 'La table subscriptions est absente. Lancez la migration 002_create_subscriptions.sql.';
        }

        $tiers_list   = (new Tiers())->getAll();
        $products_list = (new Product())->getAll();

        $user       = $_SESSION['user'];
        $editSub    = isset($_GET['edit']) ? $this->model->find((int)$_GET['edit']) : null;

        require_once __DIR__ . '/../views/subscriptions.php';
    }

    public function store(): void
    {
        if (!$this->subscriptionsTableExists()) {
            header('Location: ' . APP_URL . '/subscriptions?error=' . urlencode('Table subscriptions absente. Lancez la migration SQL.'));
            exit;
        }

        $tiersId   = (int)($_POST['tiers_id']   ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0) ?: null;
        $label     = trim($_POST['label'] ?? '');
        $amount    = (float)str_replace(',', '.', $_POST['amount'] ?? '0');
        $recurrence = in_array($_POST['recurrence'] ?? '', ['monthly','quarterly','annual','one_time'])
                      ? $_POST['recurrence'] : 'monthly';
        $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $endDate   = !empty($_POST['end_date'])   ? $_POST['end_date']   : null;
        $isActive  = 1;

        if ($tiersId === 0 || $amount <= 0) {
            header('Location: ' . APP_URL . '/subscriptions?error=' . urlencode('Client et montant obligatoires.'));
            exit;
        }

        // Si pas de label custom, on le laisse vide (le produit sera affiché)
        getDB()->prepare(
            "INSERT INTO subscriptions (tiers_id, product_id, label, amount, recurrence, start_date, end_date, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        )->execute([$tiersId, $productId, $label, $amount, $recurrence, $startDate, $endDate, $isActive]);

        header('Location: ' . APP_URL . '/subscriptions?message=' . urlencode('Abonnement créé.'));
        exit;
    }

    public function update(int $id): void
    {
        if (!$this->subscriptionsTableExists()) {
            header('Location: ' . APP_URL . '/subscriptions?error=' . urlencode('Table subscriptions absente. Lancez la migration SQL.'));
            exit;
        }

        $tiersId   = (int)($_POST['tiers_id']   ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0) ?: null;
        $label     = trim($_POST['label'] ?? '');
        $amount    = (float)str_replace(',', '.', $_POST['amount'] ?? '0');
        $recurrence = in_array($_POST['recurrence'] ?? '', ['monthly','quarterly','annual','one_time'])
                      ? $_POST['recurrence'] : 'monthly';
        $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $endDate   = !empty($_POST['end_date'])   ? $_POST['end_date']   : null;
        $isActive  = isset($_POST['is_active']) ? 1 : 0;

        getDB()->prepare(
            "UPDATE subscriptions SET tiers_id=?, product_id=?, label=?, amount=?, recurrence=?,
             start_date=?, end_date=?, is_active=? WHERE id=?"
        )->execute([$tiersId, $productId, $label, $amount, $recurrence, $startDate, $endDate, $isActive, $id]);

        header('Location: ' . APP_URL . '/subscriptions?message=' . urlencode('Abonnement mis à jour.'));
        exit;
    }

    public function destroy(int $id): void
    {
        if (!$this->subscriptionsTableExists()) {
            header('Location: ' . APP_URL . '/subscriptions?error=' . urlencode('Table subscriptions absente. Lancez la migration SQL.'));
            exit;
        }

        getDB()->prepare("DELETE FROM subscriptions WHERE id=?")->execute([$id]);
        header('Location: ' . APP_URL . '/subscriptions?message=' . urlencode('Abonnement supprimé.'));
        exit;
    }
}
