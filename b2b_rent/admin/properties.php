<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

// Удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Удаляем изображения
    $stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
    $stmt->execute([$id]);
    $imgs = $stmt->fetchAll();
    foreach ($imgs as $img) { $file = $_SERVER['DOCUMENT_ROOT'] . $img['image_path']; if (file_exists($file)) unlink($file); }
    $pdo->prepare("DELETE FROM property_images WHERE property_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$id]);
    header("Location: properties.php");
    exit;
}

// Добавление
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $area = (float)$_POST['area'];
    $rate = (float)$_POST['monthly_rate'];
    $status = $_POST['status'];
    $desc = trim($_POST['description']);
    $stmt = $pdo->prepare("INSERT INTO properties (name, address, area, monthly_rate, status, description) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$name, $address, $area, $rate, $status, $desc]);
    $prop_id = $pdo->lastInsertId();

    // Загрузка фото
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/b2b_rent/uploads/properties/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {
            if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) continue;
            $ext = strtolower(pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION));
            $new_name = uniqid() . '_' . time() . '.' . $ext;
            move_uploaded_file($tmp, $upload_dir . $new_name);
            $is_main = ($key == 0) ? 1 : 0;
            $pdo->prepare("INSERT INTO property_images (property_id, image_path, is_main, sort_order) VALUES (?,?,?,?)")
               ->execute([$prop_id, '/b2b_rent/uploads/properties/' . $new_name, $is_main, $key]);
        }
    }
    header("Location: properties.php");
    exit;
}

$properties = $pdo->query("SELECT * FROM properties ORDER BY id DESC")->fetchAll();
include '../includes/header.php'; ?>
<h2>Управление объектами</h2>
<button class="btn" onclick="document.getElementById('addForm').style.display='block'">Добавить объект</button>
<div id="addForm" style="display:none; margin-top:20px; border:1px solid #ccc; padding:15px;">
    <h3>Добавить объект</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="add" value="1">
        <div class="form-group"><label>Название:</label><input type="text" name="name" required></div>
        <div class="form-group"><label>Адрес:</label><textarea name="address" required></textarea></div>
        <div class="form-group"><label>Площадь (м²):</label><input type="number" step="0.01" name="area" required></div>
        <div class="form-group"><label>Ставка (руб/мес):</label><input type="number" step="0.01" name="monthly_rate" required></div>
        <div class="form-group"><label>Статус:</label><select name="status"><option value="free">Свободно</option><option value="rented">Арендовано</option><option value="repair">Ремонт</option></select></div>
        <div class="form-group"><label>Описание:</label><textarea name="description"></textarea></div>
        <div class="form-group"><label>Изображения (можно несколько, первое - обложка):</label><input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"></div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <button type="button" class="btn" onclick="document.getElementById('addForm').style.display='none'">Отмена</button>
    </form>
</div>
<div class="table-container">
    <table><thead><tr><th>ID</th><th>Обложка</th><th>Название</th><th>Ставка</th><th>Статус</th><th>Действия</th></tr></thead>
    <tbody><?php foreach ($properties as $p): 
        $cover = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? AND is_main=1 LIMIT 1");
        $cover->execute([$p['id']]); $cover_img = $cover->fetchColumn();
        if(!$cover_img){ $cover=$pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1"); $cover->execute([$p['id']]); $cover_img=$cover->fetchColumn(); }
    ?>
    <tr><td><?=$p['id']?></td><td><?php if($cover_img):?><img src="<?=$cover_img?>" style="width:50px;height:50px;object-fit:cover;"><?php else:?>—<?php endif;?></td>
    <td><?=htmlspecialchars($p['name'])?></td><td><?=number_format($p['monthly_rate'],0,',',' ')?> руб.</td>
    <td><?=$p['status']?></td>
    <td><a href="edit_property.php?id=<?=$p['id']?>" class="btn btn-sm btn-warning">Редактировать</a> <a href="?delete=<?=$p['id']?>" onclick="return confirm('Удалить?')" class="btn btn-sm btn-danger">Удалить</a></td></tr>
    <?php endforeach; ?></tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>