<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../services/PaymentAnalyzerService.php';

class PaymentsController
{
    public function index(): void
    {
        $paymentModel = new Payment();
        $analyzer     = new PaymentAnalyzerService();

        $methodsBreakdown   = $paymentModel->getMethodsBreakdown();
        $frequencyDist      = $analyzer->getFrequencyDistribution();
        $recentPayments     = $paymentModel->getRecentPayments(30);
        $monthlyTotals      = $paymentModel->getMonthlyTotals(12);
        $totalCollected     = $paymentModel->getTotalCollected();
        $user               = $_SESSION['user'];

        require_once __DIR__ . '/../views/payments.php';
    }
}
