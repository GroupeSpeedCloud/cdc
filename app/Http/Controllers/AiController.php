<?php

namespace App\Http\Controllers;

use App\Services\OllamaCloudService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index()
    {
        return view('ai.index');
    }

    public function summary(Request $request, OllamaCloudService $ollama)
    {
        $data = [
            'clients' => \App\Models\Client::all(),
            'services' => \App\Models\Service::all(),
            'revenues' => \App\Models\Revenue::all(),
            'expenses' => \App\Models\Expense::all(),
            'forecasts' => \App\Models\Forecast::all(),
        ];
        $summary = $ollama->summarize($data);
        return view('ai.summary', compact('summary'));
    }

    public function analyze(Request $request, OllamaCloudService $ollama)
    {
        $data = [
            'clients' => \App\Models\Client::all(),
            'services' => \App\Models\Service::all(),
            'revenues' => \App\Models\Revenue::all(),
            'expenses' => \App\Models\Expense::all(),
            'forecasts' => \App\Models\Forecast::all(),
        ];
        $result = $ollama->analyzeFinances($data);
        return view('ai.analyze', compact('result'));
    }

    public function anomalies(Request $request, OllamaCloudService $ollama)
    {
        $data = [
            'clients' => \App\Models\Client::all(),
            'services' => \App\Models\Service::all(),
            'revenues' => \App\Models\Revenue::all(),
            'expenses' => \App\Models\Expense::all(),
            'forecasts' => \App\Models\Forecast::all(),
        ];
        $anomalies = $ollama->detectAnomalies($data);
        return view('ai.anomalies', compact('anomalies'));
    }
}
