<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Product.php';

class ProductsController
{
    private Product $model;

    private function createSubscriptionsFallbackTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tiers_id`    INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED NULL,
  `label`       VARCHAR(255) NOT NULL DEFAULT '',
  `amount`      DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `recurrence`  ENUM('monthly','quarterly','annual','one_time') NOT NULL DEFAULT 'monthly',
  `start_date`  DATE NULL,
  `end_date`    DATE NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sub_tiers`   (`tiers_id`),
  KEY `idx_sub_product` (`product_id`),
  KEY `idx_sub_active`  (`is_active`),
  KEY `idx_sub_rec`     (`recurrence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        getDB()->exec($sql);
    }

    private function ensureSubscriptionsTable(): void
    {
        if ($this->subscriptionsTableExists()) {
            return;
        }

        try {
            $migrationFile = __DIR__ . '/../database/migrations/002_create_subscriptions.sql';
            if (!file_exists($migrationFile)) {
                return;
            }

            $sql = file_get_contents($migrationFile);
            if ($sql === false || trim($sql) === '') {
                return;
            }

            getDB()->exec($sql);
        } catch (Throwable $e) {
            error_log('ProductsController::ensureSubscriptionsTable error: ' . $e->getMessage());
        }

        if (!$this->subscriptionsTableExists()) {
            try {
                $this->createSubscriptionsFallbackTable();
            } catch (Throwable $e) {
                error_log('ProductsController::createSubscriptionsFallbackTable error: ' . $e->getMessage());
            }
        }
    }

    private function subscriptionsTableExists(): bool
    {
        try {
            $stmt = getDB()->query(
                "SELECT COUNT(*)
                 FROM information_schema.tables
                 WHERE table_schema = DATABASE()
                   AND table_name = 'subscriptions'"
            );
            return (int)$stmt->fetchColumn() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function __construct()
    {
        $this->model = new Product();
    }

    public function index(): void
    {
        $this->ensureSubscriptionsTable();

        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 40;
        $offset = ($page - 1) * $limit;

        $products = $this->model->getPagedWithSubscriptionStats(
            $search,
            $limit,
            $offset,
            $this->subscriptionsTableExists()
        );

        $total = $this->model->countSearch($search);
        $pages = max(1, (int)ceil($total / $limit));

        $user        = $_SESSION['user'];
        $editProduct = isset($_GET['edit']) ? $this->model->find((int)$_GET['edit']) : null;

        require_once __DIR__ . '/../views/products.php';
    }

    public function store(): void
    {
        $ref   = trim($_POST['ref']   ?? '');
        $label = trim($_POST['label'] ?? '');
        $price = (float)str_replace(',', '.', $_POST['price'] ?? '0');
        $type  = (int)($_POST['type'] ?? 0);

        if ($label === '') {
            header('Location: ' . APP_URL . '/products?error=' . urlencode('Le libellé est obligatoire.'));
            exit;
        }

        $pdo = getDB();
        $pdo->prepare(
            "INSERT INTO products (ref, label, price, type) VALUES (?, ?, ?, ?)"
        )->execute([$ref, $label, $price, $type]);

        header('Location: ' . APP_URL . '/products?message=' . urlencode('Produit créé.'));
        exit;
    }

    public function update(int $id): void
    {
        $ref   = trim($_POST['ref']   ?? '');
        $label = trim($_POST['label'] ?? '');
        $price = (float)str_replace(',', '.', $_POST['price'] ?? '0');
        $type  = (int)($_POST['type'] ?? 0);

        if ($label === '') {
            header('Location: ' . APP_URL . '/products?error=' . urlencode('Le libellé est obligatoire.'));
            exit;
        }

        $pdo = getDB();
        $pdo->prepare(
            "UPDATE products SET ref=?, label=?, price=?, type=? WHERE id=?"
        )->execute([$ref, $label, $price, $type, $id]);

        header('Location: ' . APP_URL . '/products?message=' . urlencode('Produit mis à jour.'));
        exit;
    }

    public function destroy(int $id): void
    {
        getDB()->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
        header('Location: ' . APP_URL . '/products?message=' . urlencode('Produit supprimé.'));
        exit;
    }
}
