<?php
require_once __DIR__ . "/db.php";

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { die("Bad id"); }

$stmt = $pdo->prepare("SELECT id, name, category, price, quantity FROM items WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { die("Not found"); }
?>
<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Редагування товару</title>
  <style>
    body{font-family:Arial;max-width:700px;margin:24px auto;padding:0 12px;}
    form{border:1px solid #ddd;border-radius:10px;padding:16px;}
    input{padding:10px;width:100%;margin:6px 0 12px;box-sizing:border-box;}
    button{padding:10px 14px;cursor:pointer;}
  </style>
</head>
<body>

<h2>✏️ Редагувати товар #<?= (int)$item["id"] ?></h2>

<form method="post" action="update_item.php">
  <input type="hidden" name="id" value="<?= (int)$item["id"] ?>">

  <label>Назва</label>
  <input name="name" required maxlength="150" value="<?= e($item["name"]) ?>">

  <label>Категорія</label>
  <input name="category" required maxlength="100" value="<?= e($item["category"]) ?>">

  <label>Ціна</label>
  <input name="price" type="number" step="0.01" min="0" required value="<?= e((string)$item["price"]) ?>">

  <label>Кількість</label>
  <input name="quantity" type="number" step="1" min="0" required value="<?= e((string)$item["quantity"]) ?>">

  <button type="submit">Зберегти</button>
  <a href="index.php" style="margin-left:10px;">⬅ Назад</a>
</form>

</body>
</html>
