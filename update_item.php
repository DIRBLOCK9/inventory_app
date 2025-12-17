<?php
require_once __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: index.php");
  exit;
}

$id = (int)($_POST["id"] ?? 0);
$name = trim((string)($_POST["name"] ?? ""));
$category = trim((string)($_POST["category"] ?? ""));
$price = trim((string)($_POST["price"] ?? ""));
$quantity = trim((string)($_POST["quantity"] ?? ""));

if ($id <= 0) { header("Location: index.php?err=bad_id"); exit; }
if ($name === "" || mb_strlen($name) > 150) { header("Location: edit_item.php?id=$id&err=name"); exit; }
if ($category === "" || mb_strlen($category) > 100) { header("Location: edit_item.php?id=$id&err=category"); exit; }
if (!is_numeric($price) || (float)$price < 0) { header("Location: edit_item.php?id=$id&err=price"); exit; }
if (!ctype_digit($quantity) || (int)$quantity < 0) { header("Location: edit_item.php?id=$id&err=quantity"); exit; }

$stmt = $pdo->prepare("
  UPDATE items
  SET name=?, category=?, price=?, quantity=?
  WHERE id=?
");
$stmt->execute([$name, $category, (float)$price, (int)$quantity, $id]);

header("Location: index.php?okupd=1");
exit;

