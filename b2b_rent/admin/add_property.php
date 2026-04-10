<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $area = (float)$_POST['area'];
    $rate = (float)$_POST['monthly_rate'];
    $status = $_POST['status'];
    $desc = trim($_POST['description']);

    if (empty($name) || empty($address) || $area <= 0 || $rate <= 0) {
        $error = 'Заполните все обязательные поля.';
    } else {
        // Вставляем объект
        $stmt = $pdo->prepare("INSERT INTO properties (name, address, area, monthly_rate, status, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $address, $area, $rate, $status, $desc]);
        $property_id = $pdo->lastInsertId();

        // Обработка загруженных файлов
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/b2b_rent/uploads/properties/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            $max_size = 5 * 1024 * 1024;
            $sort = 0;

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp) {
                if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) continue;
                $type = $_FILES['images']['type'][$key];
                $size = $_FILES['images']['size'][$key];
                if (!in_array($type, $allowed)) {
                    $error .= "Файл {$_FILES['images']['name'][$key]} имеет недопустимый тип.<br>";
                    continue;
                }
                if ($size > $max_size) {
                    $error .= "Файл {$_FILES['images']['name'][$key]} превышает 5 МБ.<br>";
                    continue;
                }
                $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $new_name = uniqid() . '_' . time() . '.' . $ext;
                $dest = $upload_dir . $new_name;
                if (move_uploaded_file($tmp, $dest)) {
                    $is_main = ($key == 0) ? 1 : 0; // первое фото – обложка
                    $imgStmt = $pdo->prepare("INSERT INTO property_images (property_id, image_path, is_main, sort_order) VALUES (?, ?, ?, ?)");
                    $imgStmt->execute([$property_id, '/b2b_rent/uploads/properties/' . $new_name, $is_main, $sort++]);
                } else {
                    $error .= "Не удалось загрузить {$_FILES['images']['name'][$key]}.<br>";
                }
            }
        }

        if (empty($error)) {
            header("Location: properties.php");
            exit;
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<h1>Добавление объекта</h1>
<?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <div class="form-group"><label>Название *</label><input type="text" name="name" required></div>
    <div class="form-group"><label>Адрес *</label><textarea name="address" required></textarea></div>
    <div class="form-group"><label>Площадь (м²) *</label><input type="number" step="0.01" name="area" required></div>
    <div class="form-group"><label>Ставка (руб/мес) *</label><input type="number" step="0.01" name="monthly_rate" required></div>
    <div class="form-group"><label>Статус</label>
        <select name="status">
            <option value="free">Свободно</option>
            <option value="rented">Арендовано</option>
            <option value="repair">Ремонт</option>
        </select>
    </div>
    <div class="form-group"><label>Описание</label><textarea name="description"></textarea></div>
    <div class="form-group">
        <label>Изображения (можно несколько, первое будет обложкой)</label>
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
        <small>Максимум 5 МБ на файл.</small>
    </div>
    <button type="submit" class="btn btn-success">Сохранить</button>
    <a href="properties.php" class="btn">Отмена</a>
</form>
<?php include '../includes/footer.php'; ?>