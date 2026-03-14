<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

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
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'uploads/' . $filename;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, image_path, author_id) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$title, $content, $image_path, $_SESSION['user_id']])) {
            $success = 'Пост опубликован!';
        }
    }
}

include '../includes/header.php';
?>

<div class="container" style="max-width: 800px; margin: 2rem auto;">
    <h1 style="margin-bottom: 2rem;">✍️ Создать пост</h1>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="success">
            <?php echo $success; ?>
            <p><a href="my-posts.php">Перейти к моим постам</a></p>
        </div>
    <?php endif; ?>
    
    <?php if(!$success): ?>
        <form method="POST" enctype="multipart/form-data" style="background: white; padding: 2rem; border-radius: 12px;">
            <div class="form-group">
                <label>Заголовок</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Текст</label>
                <textarea name="content" required style="min-height: 200px;"></textarea>
            </div>
            
            <div class="form-group">
                <label>Изображение (необязательно)</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="btn">Опубликовать</button>
            <a href="index.php" class="btn" style="background: #6b7280; width: auto; margin-top: 1rem;">Отмена</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>