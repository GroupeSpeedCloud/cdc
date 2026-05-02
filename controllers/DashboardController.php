<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Tiers.php';
require_once __DIR__ . '/../services/KPIService.php';

class DashboardController
{
    public function index(): void
    {
        $kpiService = new KPIService();
        $kpis       = $kpiService->getAll();
        $user       = $_SESSION['user'];

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
