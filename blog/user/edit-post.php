<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND author_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: my-posts.php');
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
        $image_path = $post['image_path'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/';
            
            if (!empty($post['image_path'])) {
                $old_file = '../' . $post['image_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'uploads/' . $filename;
            }
        }
        
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image_path = ? WHERE id = ?");
        if ($stmt->execute([$title, $content, $image_path, $post_id])) {
            $success = 'Пост обновлен!';
        }
    }
}

include '../includes/header.php';
?>

<div class="container" style="max-width: 800px; margin: 2rem auto;">
    <h1 style="margin-bottom: 2rem;">✏️ Редактировать пост</h1>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" style="background: white; padding: 2rem; border-radius: 12px;">
        <div class="form-group">
            <label>Заголовок</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Текст</label>
            <textarea name="content" required style="min-height: 200px;"><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        
        <?php if(!empty($post['image_path'])): ?>
            <div style="margin-bottom: 1rem;">
                <img src="../<?php echo $post['image_path']; ?>" style="max-width: 200px; border-radius: 8px;">
            </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label>Новое изображение</label>
            <input type="file" name="image" accept="image/*">
        </div>
        
        <button type="submit" class="btn">Сохранить</button>
        <a href="my-posts.php" class="btn" style="background: #6b7280; width: auto; margin-top: 1rem;">Назад</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>