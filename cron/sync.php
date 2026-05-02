#!/usr/bin/env php
<?php
/**
 * Cron : synchronisation Dolibarr → base locale
 * Planifier : 0 * * * * php /path/to/flow/cron/sync.php >> /var/log/flow_sync.log 2>&1
 */

define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/app.php';
require_once ROOT . '/config/database.php';
require_once ROOT . '/services/DolibarrService.php';

$start = microtime(true);
echo '[' . date('Y-m-d H:i:s') . '] Démarrage de la synchronisation Dolibarr' . PHP_EOL;

try {
    $service = new DolibarrService();
    $results = $service->syncAll();

    foreach ($results as $entity => $res) {
        printf(
            '[%s] %-12s → %d traités, %d erreurs%s',
            date('H:i:s'),
            $entity,
            $res['processed'],
            $res['failed'],
            PHP_EOL
        );
    }
} catch (Throwable $e) {
    echo '[' . date('Y-m-d H:i:s') . '] ERREUR : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

$elapsed = round(microtime(true) - $start, 2);
echo '[' . date('Y-m-d H:i:s') . "] Synchronisation terminée en {$elapsed}s" . PHP_EOL;
exit(0);
