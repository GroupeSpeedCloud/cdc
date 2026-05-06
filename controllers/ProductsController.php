<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Product.php';

class ProductsController
{
    private Product $model;

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
    }

    private function subscriptionsTableExists(): bool
    {
        try {
            $stmt = getDB()->prepare('SHOW TABLES LIKE ?');
            $stmt->execute(['subscriptions']);
            return (bool)$stmt->fetchColumn();
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
