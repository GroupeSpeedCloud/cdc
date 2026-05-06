<?php
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Product.php';

class ProductsController
{
    private Product $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 40;
        $offset = ($page - 1) * $limit;

        $pdo = getDB();
        $where  = $search ? "WHERE label LIKE ? OR ref LIKE ?" : '';
        $params = $search ? ["%$search%", "%$search%"] : [];

        $products = $this->model->query(
            "SELECT p.*,
                    (SELECT COUNT(*) FROM subscriptions s WHERE s.product_id = p.id AND s.is_active = 1) AS sub_count,
                    (SELECT COALESCE(SUM(s.amount),0) FROM subscriptions s WHERE s.product_id = p.id AND s.is_active = 1 AND s.recurrence='monthly') AS mrr_direct
             FROM products p $where
             ORDER BY p.label
             LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        );

        $total = (int)$this->model->queryOne(
            "SELECT COUNT(*) FROM products $where LIMIT 1",
            $params
        )['COUNT(*)'];
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
