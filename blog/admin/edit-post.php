<?php
session_start();
require_once 'check_admin.php';
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: posts.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: posts.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (empty($title) || empty($content)) {
        $error = 'Заполните все поля';
    } else {
        $image_path = $post['image_path'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/';
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (in_array($_FILES['image']['type'], $allowed)) {
                if (!empty($post['image_path'])) {
                    $old_image = '../' . $post['image_path'];
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = time() . '_' . uniqid() . '.' . $ext;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    $image_path = 'uploads/' . $filename;
                } else {
                    $error = 'Ошибка при загрузке файла';
                }
            } else {
                $error = 'Разрешены только JPG, PNG и GIF';
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image_path = ? WHERE id = ?");
                $stmt->execute([$title, $content, $image_path, $id]);
                $success = 'Статья успешно обновлена!';
                
                $post['title'] = $title;
                $post['content'] = $content;
                $post['image_path'] = $image_path;
                
            } catch(PDOException $e) {
                $error = 'Ошибка при обновлении: ' . $e->getMessage();
            }
        }
    }
}

?>
<link rel="stylesheet" href="admin-style.css">

<div class="admin-header">
    <div class="container">
        <a href="posts.php" class="logo">← К списку статей</a>
        <div class="admin-user">
            <span>👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
    </div>
</div>

<div class="admin-container">
    <h1>✏️ Редактирование статьи</h1>
    
    <?php if($error): ?>
        <div class="alert alert-error">
            <span>❌</span> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success">
            <span>✅</span> <?php echo $success; ?>
            <br>
            <a href="posts.php" class="btn btn-small btn-success" style="margin-top: 10px;">Вернуться к списку</a>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Заголовок статьи</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Текст статьи</label>
            <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        
        <?php if(!empty($post['image_path'])): ?>
            <div class="current-image">
                <label>Текущее изображение:</label>
                <img src="../<?php echo $post['image_path']; ?>" alt="Текущее изображение">
                <p><small>Оставьте поле пустым, чтобы оставить текущее изображение</small></p>
            </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label>Новое изображение (необязательно)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <span>💾</span> Сохранить изменения
            </button>
            <a href="posts.php" class="btn btn-secondary">
                <span>↩️</span> Отмена
            </a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>