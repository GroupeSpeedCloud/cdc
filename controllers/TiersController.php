<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Tiers.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../services/RiskScoringService.php';
require_once __DIR__ . '/../services/PaymentAnalyzerService.php';

class TiersController
{
    private Tiers $tiersModel;
    private RiskScoringService $riskService;

    public function __construct()
    {
        $this->tiersModel  = new Tiers();
        $this->riskService = new RiskScoringService();
    }

    public function index(): void
    {
        $search = htmlspecialchars(trim($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8');
        $level  = in_array($_GET['level'] ?? '', ['low', 'medium', 'high']) ? $_GET['level'] : '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $tiers = $this->tiersModel->getWithStats($limit, $offset, $search);
        $total = $this->tiersModel->count();
        $pages = (int)ceil($total / $limit);
        $user  = $_SESSION['user'];

        require_once __DIR__ . '/../views/tiers.php';
    }

    public function detail(int $id): void
    {
        $tiers = $this->tiersModel->getDetail($id);
        if (!$tiers) {
            http_response_code(404);
            echo '<p>Tiers introuvable.</p>';
            return;
        }

        $invoiceModel  = new Invoice();
        $paymentModel  = new Payment();
        $analyzer      = new PaymentAnalyzerService();

        $invoices     = $invoiceModel->getByTiers($id, 30);
        $payments     = $paymentModel->getByTiers($id, 30);
        $alerts       = $this->riskService->getAlertsForTiers($id);
        $delayStats   = $analyzer->getDelayStats($id);
        $mainMethod   = $analyzer->getMainMethodByAmount($id);
        $frequency    = $analyzer->detectFrequency($id);
        $revenueHist  = $this->tiersModel->getRevenueHistory($id, 12);
        $riskScore    = $tiers['risk_score'] ?? $this->riskService->calculateScore($id);
        $riskLevel    = $tiers['risk_level'] ?? $this->riskService->getRiskLevel((int)$riskScore);
        $user         = $_SESSION['user'];

        require_once __DIR__ . '/../views/tiers_detail.php';
    }

    public function store(): void
    {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '') {
            header('Location: ' . APP_URL . '/tiers?error=' . urlencode('Le nom du tiers est obligatoire.'));
            exit;
        }

        $pdo = getDB();
        $pdo->prepare(
            "INSERT INTO tiers (name, email, phone, address, is_active, risk_score, risk_level)
             VALUES (?, ?, ?, ?, 1, 0, 'low')"
        )->execute([$name, $email, $phone, $address]);

        header('Location: ' . APP_URL . '/tiers?message=' . urlencode('Tiers "' . $name . '" ajouté.'));
        exit;
    }

    public function update(int $id): void
    {
        $tiers = $this->tiersModel->findById($id);
        if (!$tiers) {
            header('Location: ' . APP_URL . '/tiers?error=' . urlencode('Tiers introuvable.'));
            exit;
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '') {
            header('Location: ' . APP_URL . '/tiers?error=' . urlencode('Le nom du tiers est obligatoire.'));
            exit;
        }

        $pdo = getDB();
        $pdo->prepare(
            "UPDATE tiers SET name=?, email=?, phone=?, address=?, updated_at=NOW() WHERE id=?"
        )->execute([$name, $email, $phone, $address, $id]);

        header('Location: ' . APP_URL . '/tiers?message=' . urlencode('Tiers mis à jour.'));
        exit;
    }

    public function destroy(int $id): void
    {
        $pdo = getDB();
        // Désassocier les factures et paiements avant suppression
        $pdo->prepare("UPDATE invoices  SET tiers_id = NULL WHERE tiers_id = ?")->execute([$id]);
        $pdo->prepare("UPDATE payments  SET tiers_id = NULL WHERE tiers_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM tiers WHERE id = ?")->execute([$id]);

        header('Location: ' . APP_URL . '/tiers?message=' . urlencode('Tiers supprimé.'));
        exit;
    }
}
