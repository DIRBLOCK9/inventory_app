<?php
require_once __DIR__ . "/db.php";

header("Content-Type: application/json; charset=UTF-8");

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    $stmt = $pdo->query("
        SELECT id, name, category, price, quantity, created_at
        FROM items
        ORDER BY id DESC
    ");
    $items = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("
        SELECT id, name, category, price, quantity, created_at
        FROM items
        WHERE name LIKE ? OR category LIKE ?
        ORDER BY id DESC
    ");
    $like = "%$q%";
    $stmt->execute([$like, $like]);
    $items = $stmt->fetchAll();
}

echo json_encode([
    "count" => count($items),
    "items" => $items
], JSON_UNESCAPED_UNICODE);
