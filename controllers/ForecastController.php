<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../services/ForecastService.php';

class ForecastController
{
    public function index(): void
    {
        $forecastService = new ForecastService();
        $data            = $forecastService->getAllProjections();
        $user            = $_SESSION['user'];

        require_once __DIR__ . '/../views/forecast.php';
    }
}
