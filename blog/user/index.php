<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM posts WHERE author_id = ?");
$stmt->execute([$user_id]);
$posts_count = $stmt->fetch()['total'];


$stmt = $pdo->prepare("SELECT * FROM posts WHERE author_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_posts = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="user-panel" style="max-width: 1200px; margin: 2rem auto;">

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 2rem; color: white; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">👤 Личный кабинет</h1>
                <p style="font-size: 1.2rem;"><?php echo htmlspecialchars($user['username']); ?></p>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div>
                <a href="create-post.php" class="btn" style="background: white; color: #667eea; width: auto; padding: 1rem 2rem;">➕ Новая запись</a>
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem;">📝</div>
            <div style="color: #666;">Мои посты</div>
            <div style="font-size: 2rem; font-weight: bold; color: #667eea;"><?php echo $posts_count; ?></div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <a href="my-posts.php" style="background: white; padding: 2rem; border-radius: 12px; text-decoration: none; color: #333; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <div style="font-size: 2rem;">📋</div>
            <div style="font-weight: bold;">Мои посты</div>
        </a>
        <a href="create-post.php" style="background: white; padding: 2rem; border-radius: 12px; text-decoration: none; color: #333; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <div style="font-size: 2rem;">✍️</div>
            <div style="font-weight: bold;">Написать пост</div>
        </a>
    </div>
 
    <?php if(!empty($recent_posts)): ?>
        <h2 style="margin-bottom: 1rem;">Последние публикации</h2>
        <div style="display: grid; gap: 1rem;">
            <?php foreach($recent_posts as $post): ?>
                <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3><a href="../post.php?id=<?php echo $post['id']; ?>" style="color: #333; text-decoration: none;"><?php echo htmlspecialchars($post['title']); ?></a></h3>
                            <div style="color: #666;"><?php echo date('d.m.Y', strtotime($post['created_at'])); ?></div>
                        </div>
                        <div>
                            <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn-small">✏️</a>
                            <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Удалить?')">🗑️</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>