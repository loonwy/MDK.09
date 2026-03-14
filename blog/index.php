<?php
require_once 'config/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 5;
$offset = ($page - 1) * $limit;

$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_pages = ceil($total_posts / $limit);

$stmt = $pdo->prepare("
    SELECT p.*, u.username 
    FROM posts p
    JOIN users u ON p.author_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll();

include 'includes/header.php';
?>

<h1>Последние записи</h1>

<div class="posts">
    <?php if(empty($posts)): ?>
        <p>Пока нет ни одной статьи.</p>
    <?php endif; ?>
    
    <?php foreach($posts as $post): ?>
        <article class="post-card">
            <?php if(!empty($post['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Изображение" class="post-image">
            <?php endif; ?>
            
            <h2>
                <a href="post.php?id=<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
            </h2>
            
            <div class="post-meta">
                <span>Автор: <?php echo htmlspecialchars($post['username']); ?></span>
                <span>Дата: <?php echo date('d.m.Y', strtotime($post['created_at'])); ?></span>
            </div>
            
            <div class="post-excerpt">
                <?php 
                $excerpt = strip_tags($post['content']);
                $excerpt = mb_substr($excerpt, 0, 200, 'UTF-8');
                echo htmlspecialchars($excerpt) . '...';
                ?>
            </div>
            
            <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Читать далее</a>
        </article>
    <?php endforeach; ?>
</div>

<?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>" class="pagination-link">← Предыдущая</a>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="pagination-number <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        
        <?php if($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>" class="pagination-link">Следующая →</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>