<?php
require_once __DIR__ . "/db.php";

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
  header("Location: index.php?err=bad_id");
  exit;
}

$stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php?okdel=1");
exit;

