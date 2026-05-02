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
}
