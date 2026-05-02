<?php
class DolibarrService
{
    private string $baseUrl;
    private string $apiKey;
    private PDO    $pdo;
    private int    $maxRetries = 3;

    public function __construct()
    {
        $this->baseUrl = DOLIBARR_URL . '/api/index.php';
        $this->apiKey  = DOLIBARR_API_KEY;
        $this->pdo     = getDB();
    }

    public function syncAll(): array
    {
        $results = [];
        $results['tiers']    = $this->syncThirdParties();
        $results['products'] = $this->syncProducts();
        $results['invoices'] = $this->syncInvoices();
        $results['payments'] = $this->syncPayments();
        return $results;
    }

    public function forceSync(): array
    {
        // Delete last sync timestamps to force full sync
        $this->pdo->exec("DELETE FROM settings WHERE key_name LIKE 'last_sync_%'");
        return $this->syncAll();
    }

    public function syncThirdParties(): array
    {
        $logId = $this->startLog('tiers');
        $processed = 0;
        $failed    = 0;

        try {
            $lastSync = $this->getLastSync('tiers');
            $page     = 0;
            $limit    = 100;

            do {
                $params = ['limit' => $limit, 'page' => $page, 'sortfield' => 't.rowid', 'sortorder' => 'ASC'];
                if ($lastSync) {
                    $params['sqlfilters'] = "(t.tms:>:'" . $lastSync . "')";
                }

                $data = $this->apiGet('/thirdparties', $params);
                if ($data === null) break;
                if (empty($data)) break;

                foreach ($data as $item) {
                    try {
                        $this->upsertTiers($item);
                        $processed++;
                    } catch (Exception $e) {
                        error_log('syncThirdParties item error: ' . $e->getMessage());
                        $failed++;
                    }
                }
                $page++;
            } while (count($data) === $limit);

            $this->setLastSync('tiers');
            $this->endLog($logId, 'success', "Sync tiers OK", $processed, $failed);
        } catch (Exception $e) {
            $this->endLog($logId, 'error', $e->getMessage(), $processed, $failed);
            error_log('syncThirdParties error: ' . $e->getMessage());
        }

        return ['processed' => $processed, 'failed' => $failed];
    }

    public function syncProducts(): array
    {
        $logId = $this->startLog('products');
        $processed = 0;
        $failed    = 0;

        try {
            $page  = 0;
            $limit = 100;

            do {
                $data = $this->apiGet('/products', ['limit' => $limit, 'page' => $page]);
                if ($data === null || empty($data)) break;

                foreach ($data as $item) {
                    try {
                        $this->upsertProduct($item);
                        $processed++;
                    } catch (Exception $e) {
                        $failed++;
                    }
                }
                $page++;
            } while (count($data) === $limit);

            $this->endLog($logId, 'success', "Sync products OK", $processed, $failed);
        } catch (Exception $e) {
            $this->endLog($logId, 'error', $e->getMessage(), $processed, $failed);
        }

        return ['processed' => $processed, 'failed' => $failed];
    }

    public function syncInvoices(): array
    {
        $logId = $this->startLog('invoices');
        $processed = 0;
        $failed    = 0;

        try {
            $lastSync = $this->getLastSync('invoices');
            $page     = 0;
            $limit    = 100;

            do {
                $params = ['limit' => $limit, 'page' => $page, 'sortfield' => 'f.rowid', 'sortorder' => 'ASC'];
                if ($lastSync) {
                    $params['sqlfilters'] = "(f.tms:>:'" . $lastSync . "')";
                }

                $data = $this->apiGet('/invoices', $params);
                if ($data === null || empty($data)) break;

                foreach ($data as $item) {
                    try {
                        $this->upsertInvoice($item);
                        $processed++;
                    } catch (Exception $e) {
                        error_log('syncInvoices item error: ' . $e->getMessage());
                        $failed++;
                    }
                }
                $page++;
            } while (count($data) === $limit);

            $this->setLastSync('invoices');
            $this->endLog($logId, 'success', "Sync invoices OK", $processed, $failed);
        } catch (Exception $e) {
            $this->endLog($logId, 'error', $e->getMessage(), $processed, $failed);
        }

        return ['processed' => $processed, 'failed' => $failed];
    }

    public function syncPayments(): array
    {
        $logId = $this->startLog('payments');
        $processed = 0;
        $failed    = 0;

        try {
            $page  = 0;
            $limit = 100;

            do {
                $data = $this->apiGet('/invoices/payments', ['limit' => $limit, 'page' => $page]);
                if ($data === null || empty($data)) break;

                foreach ($data as $item) {
                    try {
                        $this->upsertPayment($item);
                        $processed++;
                    } catch (Exception $e) {
                        $failed++;
                    }
                }
                $page++;
            } while (count($data) === $limit);

            $this->setLastSync('payments');
            $this->endLog($logId, 'success', "Sync payments OK", $processed, $failed);
        } catch (Exception $e) {
            $this->endLog($logId, 'error', $e->getMessage(), $processed, $failed);
        }

        return ['processed' => $processed, 'failed' => $failed];
    }

    private function apiGet(string $endpoint, array $params = []): ?array
    {
        $url = $this->baseUrl . $endpoint;
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $attempt = 0;
        while ($attempt < $this->maxRetries) {
            $context = stream_context_create([
                'http' => [
                    'method'  => 'GET',
                    'header'  => "DOLAPIKEY: {$this->apiKey}\r\nAccept: application/json\r\n",
                    'timeout' => 30,
                    'ignore_errors' => true,
                ],
            ]);

            $response = @file_get_contents($url, false, $context);
            $httpCode = 0;
            if (isset($http_response_header)) {
                preg_match('/HTTP\/\S+\s+(\d+)/', $http_response_header[0], $m);
                $httpCode = (int)($m[1] ?? 0);
            }

            if ($response !== false && $httpCode === 200) {
                $decoded = json_decode($response, true);
                return is_array($decoded) ? $decoded : [];
            }

            $attempt++;
            if ($attempt < $this->maxRetries) {
                sleep(2 * $attempt);
            }
        }

        error_log("Dolibarr API failed after {$this->maxRetries} attempts: $url");
        return null;
    }

    private function upsertTiers(array $data): void
    {
        if (empty($data['id'])) return;

        $stmt = $this->pdo->prepare(
            'INSERT INTO tiers (dolibarr_id, name, email, phone, address, is_active, created_at, updated_at)
             VALUES (:did, :name, :email, :phone, :address, :active, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
               name=VALUES(name), email=VALUES(email), phone=VALUES(phone),
               address=VALUES(address), is_active=VALUES(is_active), updated_at=NOW()'
        );
        $stmt->execute([
            'did'     => $data['id'],
            'name'    => substr($data['name'] ?? '', 0, 255),
            'email'   => substr($data['email'] ?? '', 0, 255),
            'phone'   => substr($data['phone'] ?? '', 0, 50),
            'address' => substr(($data['address'] ?? '') . ' ' . ($data['zip'] ?? '') . ' ' . ($data['town'] ?? ''), 0, 500),
            'active'  => ($data['status'] ?? 1) == 1 ? 1 : 0,
        ]);
    }

    private function upsertProduct(array $data): void
    {
        if (empty($data['id'])) return;

        $stmt = $this->pdo->prepare(
            'INSERT INTO products (dolibarr_id, ref, label, price, type, created_at, updated_at)
             VALUES (:did, :ref, :label, :price, :type, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
               ref=VALUES(ref), label=VALUES(label), price=VALUES(price),
               type=VALUES(type), updated_at=NOW()'
        );
        $stmt->execute([
            'did'   => $data['id'],
            'ref'   => substr($data['ref'] ?? '', 0, 100),
            'label' => substr($data['label'] ?? '', 0, 255),
            'price' => (float)($data['price'] ?? 0),
            'type'  => (int)($data['type'] ?? 0),
        ]);
    }

    private function upsertInvoice(array $data): void
    {
        if (empty($data['id'])) return;

        // Resolve tiers_id
        $tiersId = null;
        if (!empty($data['socid'])) {
            $s = $this->pdo->prepare('SELECT id FROM tiers WHERE dolibarr_id = ?');
            $s->execute([$data['socid']]);
            $t = $s->fetch();
            $tiersId = $t ? $t['id'] : null;
        }

        $dateInvoice = $data['date'] ? date('Y-m-d', (int)$data['date']) : null;
        $dateDue     = !empty($data['date_lim_reglement']) ? date('Y-m-d', (int)$data['date_lim_reglement']) : null;
        $datePaid    = !empty($data['date_closing']) ? date('Y-m-d', (int)$data['date_closing']) : null;
        $isOverdue   = ($dateDue && $dateDue < date('Y-m-d') && ($data['statut'] ?? 0) != 2) ? 1 : 0;

        $stmt = $this->pdo->prepare(
            'INSERT INTO invoices (dolibarr_id, ref, tiers_id, date_invoice, date_due, date_paid, total_ht, total_ttc, status, is_overdue, created_at, updated_at)
             VALUES (:did, :ref, :tiers, :di, :dd, :dp, :tht, :ttc, :status, :over, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
               ref=VALUES(ref), tiers_id=VALUES(tiers_id), date_invoice=VALUES(date_invoice),
               date_due=VALUES(date_due), date_paid=VALUES(date_paid), total_ht=VALUES(total_ht),
               total_ttc=VALUES(total_ttc), status=VALUES(status), is_overdue=VALUES(is_overdue), updated_at=NOW()'
        );
        $stmt->execute([
            'did'    => $data['id'],
            'ref'    => substr($data['ref'] ?? '', 0, 100),
            'tiers'  => $tiersId,
            'di'     => $dateInvoice,
            'dd'     => $dateDue,
            'dp'     => $datePaid,
            'tht'    => (float)($data['total_ht'] ?? 0),
            'ttc'    => (float)($data['total_ttc'] ?? 0),
            'status' => (int)($data['statut'] ?? 0),
            'over'   => $isOverdue,
        ]);

        // Sync invoice lines
        if (!empty($data['lines'])) {
            $invStmt = $this->pdo->prepare('SELECT id FROM invoices WHERE dolibarr_id = ?');
            $invStmt->execute([$data['id']]);
            $inv = $invStmt->fetch();
            if ($inv) {
                foreach ($data['lines'] as $line) {
                    $this->upsertInvoiceLine($inv['id'], $line);
                }
            }
        }
    }

    private function upsertInvoiceLine(int $invoiceId, array $line): void
    {
        $productId = null;
        if (!empty($line['fk_product'])) {
            $s = $this->pdo->prepare('SELECT id FROM products WHERE dolibarr_id = ?');
            $s->execute([$line['fk_product']]);
            $p = $s->fetch();
            $productId = $p ? $p['id'] : null;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO invoice_lines (invoice_id, product_id, description, qty, unit_price, total_ht)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE qty=VALUES(qty), unit_price=VALUES(unit_price), total_ht=VALUES(total_ht)'
        );
        $stmt->execute([
            $invoiceId,
            $productId,
            substr($line['desc'] ?? $line['product_label'] ?? '', 0, 500),
            (float)($line['qty'] ?? 1),
            (float)($line['subprice'] ?? 0),
            (float)($line['total_ht'] ?? 0),
        ]);
    }

    private function upsertPayment(array $data): void
    {
        if (empty($data['id'])) return;

        $invoiceId = null;
        if (!empty($data['fk_facture'])) {
            $s = $this->pdo->prepare('SELECT id, tiers_id FROM invoices WHERE dolibarr_id = ?');
            $s->execute([$data['fk_facture']]);
            $inv = $s->fetch();
            if ($inv) {
                $invoiceId = $inv['id'];
            }
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO payments (dolibarr_id, invoice_id, tiers_id, amount, date_payment, method, method_label, created_at)
             VALUES (:did, :inv, :tiers, :amount, :date, :method, :mlabel, NOW())
             ON DUPLICATE KEY UPDATE
               amount=VALUES(amount), date_payment=VALUES(date_payment),
               method=VALUES(method), method_label=VALUES(method_label)'
        );

        $methodLabel = $data['payment_code'] ?? $data['type_libelle'] ?? '';
        $method = $this->detectMethod($data['payment_code'] ?? '', $methodLabel);

        $stmt->execute([
            'did'    => $data['id'],
            'inv'    => $invoiceId,
            'tiers'  => $inv['tiers_id'] ?? null,
            'amount' => (float)($data['amount'] ?? 0),
            'date'   => !empty($data['datepaye']) ? date('Y-m-d', (int)$data['datepaye']) : null,
            'method' => $method,
            'mlabel' => $methodLabel,
        ]);
    }

    private function detectMethod(string $code, string $label): string
    {
        $codeUpper  = strtoupper($code);
        $labelLower = strtolower($label);

        if (in_array($codeUpper, ['CB', 'CARTE', 'CREDIT_CARD', 'VIS', 'MC'], true)) return 'CB';
        if (in_array($codeUpper, ['VIR', 'VIREMENT', 'TRANSFER', 'TRF'], true)) return 'virement';
        if (in_array($codeUpper, ['CHQ', 'CHEQUE', 'CHECK'], true)) return 'chèque';
        if (in_array($codeUpper, ['ESP', 'CASH', 'ESPECES'], true)) return 'espèces';

        if (str_contains($labelLower, 'carte') || str_contains($labelLower, 'cb') || str_contains($labelLower, 'visa')) return 'CB';
        if (str_contains($labelLower, 'virement') || str_contains($labelLower, 'transfer')) return 'virement';
        if (str_contains($labelLower, 'chèque') || str_contains($labelLower, 'cheque')) return 'chèque';
        if (str_contains($labelLower, 'espèces') || str_contains($labelLower, 'especes') || str_contains($labelLower, 'cash')) return 'espèces';

        return 'inconnu';
    }

    private function getLastSync(string $entity): ?string
    {
        $stmt = $this->pdo->prepare('SELECT value FROM settings WHERE key_name = ?');
        $stmt->execute(["last_sync_$entity"]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : null;
    }

    private function setLastSync(string $entity): void
    {
        $this->pdo->prepare(
            'INSERT INTO settings (key_name, value, updated_at) VALUES (?, ?, NOW())
             ON DUPLICATE KEY UPDATE value=VALUES(value), updated_at=NOW()'
        )->execute(["last_sync_$entity", date('Y-m-d H:i:s')]);
    }

    private function startLog(string $entity): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sync_logs (entity_type, status, message, records_processed, records_failed, started_at)
             VALUES (?, "running", "En cours...", 0, 0, NOW())'
        );
        $stmt->execute([$entity]);
        return (int)$this->pdo->lastInsertId();
    }

    private function endLog(int $logId, string $status, string $message, int $processed, int $failed): void
    {
        $this->pdo->prepare(
            'UPDATE sync_logs SET status=?, message=?, records_processed=?, records_failed=?, completed_at=NOW() WHERE id=?'
        )->execute([$status, $message, $processed, $failed, $logId]);
    }

    public function getRecentLogs(int $limit = 50): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM sync_logs ORDER BY started_at DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
