<!doctype html>
<html lang="uk">
<head>
<meta charset="utf-8">
<title>Inventory App</title>
<div class="container">
 
<style>
* {
  box-sizing: border-box;
}

body {
  font-family: "Segoe UI", Arial, sans-serif;
  background: #f6f7fb;
  margin: 0;
  padding: 30px;
  color: #333;
}

.container {
  max-width: 1100px;
  margin: auto;
  background: #fff;
  padding: 25px 30px;
  border-radius: 14px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

h2 {
  margin-top: 0;
  margin-bottom: 20px;
}

h3 {
  margin: 20px 0 10px;
}

input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
}

input:focus {
  outline: none;
  border-color: #6c8cff;
  box-shadow: 0 0 0 2px rgba(108,140,255,0.15);
}

button {
  padding: 10px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

button.primary {
  background: #6c8cff;
  color: white;
}

button.primary:hover {
  background: #5876e8;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

th {
  text-align: left;
  background: #f0f2f8;
  padding: 12px;
  font-weight: 600;
  font-size: 14px;
}

td {
  padding: 12px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
}

tr:hover {
  background: #f9faff;
}

.actions a {
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 13px;
  margin-right: 6px;
}

.actions .edit {
  background: #fff3cd;
  color: #856404;
}

.actions .delete {
  background: #f8d7da;
  color: #842029;
}

.actions .edit:hover {
  background: #ffe69c;
}

.actions .delete:hover {
  background: #f1aeb5;
}

.alert-success {
  background: #e6ffed;
  border: 1px solid #b7f0c2;
  color: #1f7a3e;
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 15px;
}

.alert-error {
  background: #ffecec;
  border: 1px solid #ffb3b3;
  color: #8a1f1f;
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 15px;
}

.search {
  margin: 20px 0;
}

</style>
</div>

</head>
<body>

<h2> Склад товарів</h2>

<?php
$ok = isset($_GET["ok"]);
$okdel = isset($_GET["okdel"]);
$okupd = isset($_GET["okupd"]);
$err = $_GET["err"] ?? "";
?>

<?php if ($ok): ?>
  <div style="background:#eaffea;border:1px solid #b7f0b7;padding:10px;border-radius:10px;margin:10px 0;">
     Товар додано
  </div>
<?php endif; ?>

<?php if ($okdel): ?>
  <div style="background:#eaffea;border:1px solid #b7f0b7;padding:10px;border-radius:10px;margin:10px 0;">
     Товар видалено
  </div>
<?php endif; ?>

<?php if ($okupd): ?>
  <div style="background:#eaffea;border:1px solid #b7f0b7;padding:10px;border-radius:10px;margin:10px 0;">
     Товар оновлено
  </div>
<?php endif; ?>

<?php if ($err): ?>
  <div style="background:#ffecec;border:1px solid #ffb3b3;padding:10px;border-radius:10px;margin:10px 0;">
     Помилка: <?= htmlspecialchars($err, ENT_QUOTES, "UTF-8") ?>
  </div>
<?php endif; ?>

<h3>➕ Додати товар</h3>
<form method="post" action="add_item.php" style="margin-bottom:18px;">
  <input name="name" placeholder="Назва товару" required maxlength="150">
  <input name="category" placeholder="Категорія" required maxlength="100">
  <input name="price" type="number" step="0.01" min="0" placeholder="Ціна" required>
  <input name="quantity" type="number" step="1" min="0" placeholder="Кількість" required>
  <button class="primary">Зберегти</button>
</form>

<hr style="margin:18px 0;">

<input id="q" placeholder="Пошук за назвою або категорією">
<div id="info" style="margin:8px 0 10px;"></div>

<table>
<thead>
<tr>
  <th>ID</th>
  <th>Назва</th>
  <th>Категорія</th>
  <th>Ціна</th>
  <th>Кількість</th>
  <th>Дата</th>
  <th>Дії</th>
</tr>
</thead>
<tbody id="rows"></tbody>
</table>

<script>
const q = document.getElementById('q');
const rows = document.getElementById('rows');
const info = document.getElementById('info');

function esc(s){
  return String(s).replace(/[&<>"']/g, c => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[c]));
}

async function load(){
  try{
    const r = await fetch('list_items.php?q=' + encodeURIComponent(q.value.trim()), {
      headers: {'Accept':'application/json'}
    });

    if(!r.ok){
      info.textContent = `Помилка API: HTTP ${r.status}`;
      rows.innerHTML = '';
      return;
    }

    const data = await r.json();

    info.textContent = `Знайдено: ${data.count ?? 0}`;

    const items = data.items ?? [];
    rows.innerHTML = items.map(i => `
      <tr>
        <td>${esc(i.id)}</td>
        <td>${esc(i.name)}</td>
        <td>${esc(i.category)}</td>
        <td>${esc(i.price)}</td>
        <td>${esc(i.quantity)}</td>
        <td>${esc(i.created_at)}</td>
        <td>
          <a class="btn" href="edit_item.php?id=${esc(i.id)}">✏️</a>
          <a class="btn" href="delete_item.php?id=${esc(i.id)}" onclick="return confirm('Точно видалити #' + ${esc(i.id)} + '?')">🗑</a>
        </td>
      </tr>
    `).join('');
  } catch(e){
    info.textContent = 'JS помилка: ' + e.message;
    rows.innerHTML = '';
  }
}

q.addEventListener('input', () => {
  clearTimeout(window.__t);
  window.__t = setTimeout(load, 200);
});

load();
</script>


</body>
</html>


