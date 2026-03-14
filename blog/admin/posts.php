<?php
require_once '../config/db.php';
require_once 'check-admin.php';

$posts = $pdo->query("
    SELECT p.*, u.username 
    FROM posts p
    JOIN users u ON p.author_id = u.id 
    ORDER BY p.created_at DESC
")->fetchAll();

include '../includes/header.php';
?>
<link rel="stylesheet" href="admin-style.css">

<div class="admin-header">
    <div class="container">
        <a href="index.php">← Панель управления</a>
    </div>
</div>

<div class="admin-container">
    <h1>Управление статьями</h1>
    
    <a href="add-post.php" class="btn" style="margin-bottom: 1rem;">➕ Добавить статью</a>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Заголовок</th>
                <th>Автор</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($posts as $post): ?>
                <tr>
                    <td><?php echo $post['id']; ?></td>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['username']); ?></td>
                    <td><?php echo date('d.m.Y', strtotime($post['created_at'])); ?></td>
                    <td>
                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn-small">✏️</a>
                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Удалить?')">🗑️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>