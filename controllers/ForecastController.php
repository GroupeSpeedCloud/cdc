<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../services/ForecastService.php';

class ForecastController
{
    public function index(): void
    {
        $data = [
            'historical' => [],
            'historical_expenses' => [],
            'ma3' => [],
            'ma6' => [],
            'proj3' => ['values' => [], 'labels' => [], 'expense_values' => [], 'net_values' => []],
            'proj6' => ['values' => [], 'labels' => [], 'expense_values' => [], 'net_values' => []],
            'proj12' => ['values' => [], 'labels' => [], 'expense_values' => [], 'net_values' => []],
            'expenses_available' => false,
            'expenses_monthly_base' => 0.0,
            'expenses_annual_equivalent' => 0.0,
            'recurring' => [],
            'trend' => 'stable',
            'health' => 0.0,
            'error_message' => null,
        ];

        try {
            $forecastService = new ForecastService();
            $data = array_merge($data, $forecastService->getAllProjections());
        } catch (Throwable $e) {
            error_log('ForecastController::index error: ' . $e->getMessage());
            $data['error_message'] = 'Impossible de charger les previsions pour le moment.';
        }
        $user            = $_SESSION['user'];

        require_once __DIR__ . '/../views/forecast.php';
    }
}
