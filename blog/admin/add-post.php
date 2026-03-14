<?php
require_once '../config/db.php';
require_once 'check-admin.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $error = 'Заполните все поля';
    } else {
        $image_path = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'uploads/' . $filename;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, image_path, author_id) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$title, $content, $image_path, $_SESSION['user_id']])) {
            $success = 'Статья добавлена!';
        }
    }
}

include '../includes/header.php';
?>
<link rel="stylesheet" href="admin-style.css">

<div class="admin-header">
    <div class="container">
        <a href="posts.php">← К статьям</a>
    </div>
</div>

<div class="admin-container">
    <h1>Добавить статью</h1>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Заголовок</label>
            <input type="text" name="title" required>
        </div>
        
        <div class="form-group">
            <label>Текст</label>
            <textarea name="content" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Изображение</label>
            <input type="file" name="image" accept="image/*">
        </div>
        
        <button type="submit" class="btn">Сохранить</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>