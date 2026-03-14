<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE author_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="container" style="max-width: 900px; margin: 2rem auto;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
        <h1>📋 Мои посты</h1>
        <a href="create-post.php" class="btn" style="width: auto;">➕ Новый пост</a>
    </div>
    
    <?php if(isset($_GET['deleted'])): ?>
        <div class="success">✅ Пост удален</div>
    <?php endif; ?>
    
    <?php if(empty($posts)): ?>
        <div style="text-align: center; padding: 4rem; background: white; border-radius: 12px;">
            <div style="font-size: 4rem;">📝</div>
            <h3>У вас пока нет постов</h3>
            <a href="create-post.php" class="btn" style="width: auto; margin-top: 1rem;">Написать первый пост</a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 1rem;">
            <?php foreach($posts as $post): ?>
                <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3><a href="../post.php?id=<?php echo $post['id']; ?>" style="color: #333; text-decoration: none;"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                            <div style="color: #666;">📅 <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></div>
                        </div>
                        <div>
                            <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn-small">✏️ Редактировать</a>
                            <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Удалить пост?')">🗑️ Удалить</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>