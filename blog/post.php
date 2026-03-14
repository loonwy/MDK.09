<?php
require_once 'config/db.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$post_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, u.username 
    FROM posts p
    JOIN users u ON p.author_id = u.id 
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$stmt->execute([$post_id]);
$likes_count = $stmt->fetchColumn();

$user_liked = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$_SESSION['user_id'], $post_id]);
    $user_liked = $stmt->fetch() ? true : false;
}

include 'includes/header.php';
?>

<article class="full-post">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    
    <div class="post-meta">
        <span>Автор: <?php echo htmlspecialchars($post['username']); ?></span>
        <span>Дата: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></span>
    </div>
    
    <?php if(!empty($post['image_path'])): ?>
        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Изображение" class="full-post-image">
    <?php endif; ?>
    
    <div class="post-content">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
</article>

<div class="like-container">
    <button class="like-btn <?php echo $user_liked ? 'liked' : ''; ?>" 
            id="likeBtn"
            data-post-id="<?php echo $post_id; ?>"
            <?php if(!isset($_SESSION['user_id'])): ?>disabled<?php endif; ?>>
        <span class="like-icon">❤️</span>
        <span class="like-count" id="likeCount"><?php echo $likes_count; ?></span>
    </button>
    <?php if(!isset($_SESSION['user_id'])): ?>
        <p class="like-message"><a href="login.php">Войдите</a>, чтобы ставить лайки</p>
    <?php endif; ?>
</div>

<section class="comments">
    <h2>Комментарии <span class="comments-count" id="commentsCount">(<?php echo count($comments); ?>)</span></h2>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <form id="comment-form" class="comment-form">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" id="postId">
            <div class="form-group">
                <textarea name="comment" id="commentText" placeholder="Ваш комментарий..." required></textarea>
            </div>
            <button type="submit" class="btn" id="submitComment">Отправить комментарий</button>
        </form>
    <?php else: ?>
        <div class="login-to-comment">
            <p>Чтобы оставить комментарий, <a href="login.php">войдите</a></p>
        </div>
    <?php endif; ?>
    
    <div id="comments-list" class="comments-list">
        <?php if(empty($comments)): ?>
            <p class="no-comments">Пока нет комментариев. Будьте первым!</p>
        <?php endif; ?>
        
        <?php foreach($comments as $comment): ?>
            <div class="comment" data-comment-id="<?php echo $comment['id']; ?>">
                <div class="comment-header">
                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <span><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></span>
                </div>
                <div class="comment-text">
                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isSubmitting = false;
    
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (isSubmitting) {
                console.log('Уже отправляется...');
                return;
            }
            
            const commentText = document.getElementById('commentText');
            const submitBtn = document.getElementById('submitComment');
            
            if (!commentText.value.trim()) {
                alert('Введите комментарий');
                return;
            }
            
            isSubmitting = true;
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправка...';
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('ajax/add_comment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const noComments = document.querySelector('.no-comments');
                    if (noComments) noComments.remove();
                    
                    const commentsList = document.getElementById('comments-list');
                    const newComment = document.createElement('div');
                    newComment.className = 'comment';
                    newComment.dataset.commentId = result.comment.id;
                    newComment.innerHTML = `
                        <div class="comment-header">
                            <strong>${result.comment.username}</strong>
                            <span>${result.comment.created_at}</span>
                        </div>
                        <div class="comment-text">${result.comment.comment}</div>
                    `;
                    
                    commentsList.insertBefore(newComment, commentsList.firstChild);
                    
                    commentText.value = '';
                    
                    const commentsCount = document.getElementById('commentsCount');
                    const currentCount = commentsList.children.length;
                    commentsCount.textContent = `(${currentCount})`;
                    
                } else {
                    alert(result.error || 'Ошибка при добавлении комментария');
                }
            } catch(error) {
                alert('Ошибка соединения');
                console.error(error);
            } finally {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
    
    // Лайки
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn && !likeBtn.disabled) {
        likeBtn.addEventListener('click', async function() {
            if (this.disabled) return;
            
            const postId = this.dataset.postId;
            const isLiked = this.classList.contains('liked');
            const action = isLiked ? 'unlike' : 'like';
            
            this.disabled = true;
            
            try {
                const formData = new FormData();
                formData.append('post_id', postId);
                formData.append('action', action);
                
                const response = await fetch('ajax/like.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.user_liked) {
                        this.classList.add('liked');
                    } else {
                        this.classList.remove('liked');
                    }
                    document.getElementById('likeCount').textContent = result.likes;
                }
            } catch(error) {
                console.error(error);
            } finally {
                this.disabled = false;
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>