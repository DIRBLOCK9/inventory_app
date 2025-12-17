<?php
require_once __DIR__ . "/db.php";
header("Content-Type: application/json; charset=UTF-8");

function out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

$q = trim((string)($_GET["q"] ?? ""));
$q = mb_substr($q, 0, 100);

$page = (int)($_GET["page"] ?? 1);
$limit = (int)($_GET["limit"] ?? 25);
if ($page < 1) $page = 1;
if (!in_array($limit, [10,25,50], true)) $limit = 25;

$offset = ($page - 1) * $limit;

try {
  if ($q === "") {
    $total = (int)$pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
    $stmt = $pdo->prepare("SELECT id,name,category,price,qty,created_at FROM items ORDER BY id DESC LIMIT :lim OFFSET :off");
    $stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
    $stmt->bindValue(":off", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll();
  } else {
    $like = "%{$q}%";
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM items WHERE name LIKE ? OR category LIKE ?");
    $stmtTotal->execute([$like,$like]);
    $total = (int)$stmtTotal->fetchColumn();

    $stmt = $pdo->prepare("
      SELECT id,name,category,price,qty,created_at
      FROM items
      WHERE name LIKE ? OR category LIKE ?
      ORDER BY id DESC
      LIMIT ? OFFSET ?
    ");
    $stmt->execute([$like,$like,$limit,$offset]);
    $items = $stmt->fetchAll();
  }

  $pages = max(1, (int)ceil($total / $limit));
  if ($page > $pages) $page = $pages;

  out([
    "page" => $page,
    "pages" => $pages,
    "limit" => $limit,
    "total" => $total,
    "items" => $items
  ]);
} catch (Throwable $e) {
  out(["error" => $e->getMessage()], 500);
}
