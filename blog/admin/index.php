<?php
require_once '../config/db.php';
require_once 'check-admin.php';

$posts_count = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$comments_count = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$recent_comments = $pdo->query("
    SELECT c.*, u.username, p.title 
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
    LIMIT 5
")->fetchAll();

include '../includes/header.php';
?>
<link rel="stylesheet" href="admin-style.css">

<div class="admin-header">
    <div class="container">
        <a href="../index.php">← На сайт</a>
        <span>Администратор: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../logout.php">Выйти</a>
    </div>
</div>

<div class="admin-container">
    <h1>Панель управления</h1>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-info">
                <h3>Статьи</h3>
                <div class="stat-number"><?php echo $posts_count; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-info">
                <h3>Комментарии</h3>
                <div class="stat-number"><?php echo $comments_count; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3>Пользователи</h3>
                <div class="stat-number"><?php echo $users_count; ?></div>
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; margin: 2rem 0; flex-wrap: wrap;">
        <a href="posts.php" class="btn">📋 Управление статьями</a>
        <a href="add-post.php" class="btn">➕ Добавить статью</a>
        <a href="comments.php" class="btn" style="background: #10b981;">💬 Управление комментариями</a>
    </div>
    
    <?php if(!empty($recent_comments)): ?>
        <h2 style="margin: 2rem 0 1rem;">Последние комментарии</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Статья</th>
                    <th>Комментарий</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_comments as $comment): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($comment['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($comment['title']); ?></td>
                        <td><?php echo htmlspecialchars(mb_substr($comment['comment'], 0, 50)); ?>...</td>
                        <td><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>