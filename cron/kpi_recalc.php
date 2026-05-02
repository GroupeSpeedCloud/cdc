#!/usr/bin/env php
<?php
/**
 * Cron : recalcul des KPI et scores de risque
 * Planifier : 30 2 * * * php /path/to/flow/cron/kpi_recalc.php >> /var/log/flow_kpi.log 2>&1
 */

define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/app.php';
require_once ROOT . '/config/database.php';
require_once ROOT . '/models/BaseModel.php';
require_once ROOT . '/models/Invoice.php';
require_once ROOT . '/models/Tiers.php';
require_once ROOT . '/models/Payment.php';
require_once ROOT . '/services/KPIService.php';
require_once ROOT . '/services/PaymentAnalyzerService.php';
require_once ROOT . '/services/RiskScoringService.php';

$start = microtime(true);
echo '[' . date('Y-m-d H:i:s') . '] Démarrage du recalcul KPI / scores de risque' . PHP_EOL;

try {
    // ── KPIs ──
    $kpiService = new KPIService();
    $kpis = $kpiService->getAll();
    foreach ($kpis as $key => $value) {
        $kpiService->cacheKpi($key, $value, date('Y-m'));
    }
    echo '[' . date('H:i:s') . '] KPIs recalculés et mis en cache (' . count($kpis) . ' indicateurs)' . PHP_EOL;

    // ── Risk scores ──
    $riskService = new RiskScoringService();
    $riskService->updateAllScores();
    echo '[' . date('H:i:s') . '] Scores de risque mis à jour pour tous les tiers' . PHP_EOL;

    // ── Summary ──
    $atRisk    = count($riskService->getAtRiskClients());
    $declining = count($riskService->getDecliningClients());
    $dormant   = count($riskService->getDormantClients());
    printf(
        '[%s] Alertes → %d tiers à risque élevé, %d en déclin, %d dormants%s',
        date('H:i:s'), $atRisk, $declining, $dormant, PHP_EOL
    );

} catch (Throwable $e) {
    echo '[' . date('Y-m-d H:i:s') . '] ERREUR : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

$elapsed = round(microtime(true) - $start, 2);
echo '[' . date('Y-m-d H:i:s') . "] Recalcul terminé en {$elapsed}s" . PHP_EOL;
exit(0);
