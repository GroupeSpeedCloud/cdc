<?php
require_once __DIR__ . '/../services/DolibarrService.php';

class SyncController
{
    private DolibarrService $dolibarr;

    public function __construct()
    {
        $this->dolibarr = new DolibarrService();
    }

    public function index(): void
    {
        $logs       = $this->dolibarr->getRecentLogs(50);
        $lastSyncs  = $this->getLastSyncs();
        $user       = $_SESSION['user'];

        require_once __DIR__ . '/../views/sync.php';
    }

    public function forceSync(): void
    {
        $results = $this->dolibarr->forceSync();
        $hasError = false;
        $hasWarning = false;
        $summary = [];

        foreach ($results as $entity => $result) {
            $status = $result['status'] ?? 'error';
            if ($status === 'error') {
                $hasError = true;
            } elseif ($status === 'warning') {
                $hasWarning = true;
            }

            $summary[] = sprintf(
                '%s: %s traités, %s erreurs',
                $entity,
                (int)($result['processed'] ?? 0),
                (int)($result['failed'] ?? 0)
            );
        }

        $message = ($hasError ? 'Synchronisation terminée avec erreurs. ' : ($hasWarning ? 'Synchronisation partielle effectuée. ' : 'Synchronisation complète effectuée. '))
            . implode(' | ', $summary);
        $queryKey = $hasError ? 'error' : ($hasWarning ? 'warning' : 'message');

        header('Location: ' . APP_URL . '/sync?' . $queryKey . '=' . urlencode($message));
        exit;
    }

    private function getLastSyncs(): array
    {
        try {
            $pdo  = getDB();
            $stmt = $pdo->query(
                "SELECT key_name, value FROM settings WHERE key_name LIKE 'last_sync_%'"
            );
            $rows   = $stmt->fetchAll();
            $result = [];
            foreach ($rows as $row) {
                $entity           = str_replace('last_sync_', '', $row['key_name']);
                $result[$entity]  = $row['value'];
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
}
