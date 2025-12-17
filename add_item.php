<?php
require_once __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: index.php");
  exit;
}

$name = trim((string)($_POST["name"] ?? ""));
$category = trim((string)($_POST["category"] ?? ""));
$price = trim((string)($_POST["price"] ?? ""));
$quantity = trim((string)($_POST["quantity"] ?? ""));

// валідація
if ($name === "" || mb_strlen($name) > 150) {
  header("Location: index.php?err=name");
  exit;
}
if ($category === "" || mb_strlen($category) > 100) {
  header("Location: index.php?err=category");
  exit;
}
if (!is_numeric($price) || (float)$price < 0) {
  header("Location: index.php?err=price");
  exit;
}
if (!ctype_digit($quantity) || (int)$quantity < 0) {
  header("Location: index.php?err=quantity");
  exit;
}

$stmt = $pdo->prepare("
  INSERT INTO items (name, category, price, quantity)
  VALUES (?, ?, ?, ?)
");
$stmt->execute([$name, $category, (float)$price, (int)$quantity]);

header("Location: index.php?ok=1");
exit;

