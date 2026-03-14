<?php
require_once '../config/db.php';
require_once 'check-admin.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: comments.php?success=deleted');
        exit;
    } catch(PDOException $e) {
        header('Location: comments.php?error=delete_failed');
        exit;
    }
}

if (isset($_POST['bulk_delete']) && isset($_POST['comment_ids'])) {
    $ids = array_map('intval', $_POST['comment_ids']);
    
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        try {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            header('Location: comments.php?success=bulk_deleted');
            exit;
        } catch(PDOException $e) {
            header('Location: comments.php?error=bulk_delete_failed');
            exit;
        }
    }
}

$comments = $pdo->query("
    SELECT c.*, u.username, p.title as post_title, p.id as post_id
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
")->fetchAll();

include '../includes/header.php';
?>
<link rel="stylesheet" href="admin-style.css">

<div class="admin-header">
    <div class="container">
        <a href="index.php">← Панель управления</a>
        <span>Администратор: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../logout.php">Выйти</a>
    </div>
</div>

<div class="admin-container">
    <h1>💬 Управление комментариями</h1>
    
    <?php if(isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
        <div class="alert alert-success">✅ Комментарий успешно удален</div>
    <?php endif; ?>
    
    <?php if(isset($_GET['success']) && $_GET['success'] == 'bulk_deleted'): ?>
        <div class="alert alert-success">✅ Выбранные комментарии удалены</div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error">❌ Ошибка при удалении комментариев</div>
    <?php endif; ?>
    
    <?php if (empty($comments)): ?>
        <div class="empty-state">
            <div class="emoji">💭</div>
            <h3>Пока нет комментариев</h3>
            <p>Когда пользователи начнут оставлять комментарии, они появятся здесь</p>
        </div>
    <?php else: ?>
        <div class="action-bar">
            <div class="action-buttons">
                <button type="button" class="btn-small" onclick="toggleAll()" style="background: #6b7280;">Выбрать все</button>
                <button type="button" class="btn-small btn-danger" onclick="deleteSelected()">Удалить выбранные</button>
            </div>
            <div>
                Всего комментариев: <strong><?php echo count($comments); ?></strong>
            </div>
        </div>
        
        <form method="POST" id="bulkForm">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="checkbox-column">
                            <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes()">
                        </th>
                        <th>ID</th>
                        <th>Статья</th>
                        <th>Пользователь</th>
                        <th>Комментарий</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($comments as $comment): ?>
                        <tr>
                            <td class="checkbox-column">
                                <input type="checkbox" name="comment_ids[]" value="<?php echo $comment['id']; ?>" class="comment-checkbox">
                            </td>
                            <td>#<?php echo $comment['id']; ?></td>
                            <td>
                                <a href="../post.php?id=<?php echo $comment['post_id']; ?>" target="_blank" style="color: var(--primary); text-decoration: none;">
                                    <?php echo htmlspecialchars(mb_substr($comment['post_title'], 0, 40)); ?>
                                    <?php if(mb_strlen($comment['post_title']) > 40): ?>...<?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                            </td>
                            <td>
                                <div style="max-width: 300px; max-height: 100px; overflow-y: auto; padding: 0.5rem; background: #f9fafb; border-radius: 4px;">
                                    <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                </div>
                            </td>
                            <td>
                                <?php echo date('d.m.Y', strtotime($comment['created_at'])); ?>
                                <br><small style="color: var(--gray);"><?php echo date('H:i', strtotime($comment['created_at'])); ?></small>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $comment['id']; ?>" 
                                   class="btn-small btn-danger"
                                   onclick="return confirm('Удалить комментарий?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php endif; ?>
</div>

<script>
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.getElementsByClassName('comment-checkbox');
    
    for(let checkbox of checkboxes) {
        checkbox.checked = selectAll.checked;
    }
}

function toggleAll() {
    const checkboxes = document.getElementsByClassName('comment-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    for(let checkbox of checkboxes) {
        checkbox.checked = !allChecked;
    }
    
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.checked = !allChecked;
    }
}

function deleteSelected() {
    const checkboxes = document.getElementsByClassName('comment-checkbox');
    const selected = Array.from(checkboxes).filter(cb => cb.checked);
    
    if (selected.length === 0) {
        alert('Выберите комментарии для удаления');
        return;
    }
    
    if (confirm(`Удалить ${selected.length} комментариев?`)) {
        document.getElementById('bulkForm').submit();
    }
}

document.getElementById('bulkForm')?.addEventListener('submit', function(e) {
    if (e.submitter && e.submitter.name === 'bulk_delete') {
        const checked = document.querySelectorAll('.comment-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Выберите комментарии для удаления');
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>