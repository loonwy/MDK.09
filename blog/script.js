document.addEventListener('DOMContentLoaded', function() {
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
        
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
            });
        });
    }
    
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn && !likeBtn.disabled) {
        likeBtn.addEventListener('click', async function() {
            const postId = this.dataset.postId;
            const isLiked = this.classList.contains('liked');
            const action = isLiked ? 'unlike' : 'like';
            
            this.disabled = true;
            
            try {
                const formData = new FormData();
                formData.append('post_id', postId);
                formData.append('action', action);
                
                const response = await fetch('/blog/ajax/like.php', {
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
    
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправка...';
            
            try {
                const response = await fetch('/blog/ajax/add_comment.php', {
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
                    newComment.innerHTML = `
                        <div class="comment-header">
                            <strong>${result.comment.username}</strong>
                            <span>${result.comment.created_at}</span>
                        </div>
                        <div class="comment-text">${result.comment.comment}</div>
                    `;
                    
                    commentsList.insertBefore(newComment, commentsList.firstChild);
                    this.reset();
                    
                    const commentsCount = document.querySelector('.comments-count');
                    if (commentsCount) {
                        const current = parseInt(commentsCount.textContent.match(/\d+/) || 0);
                        commentsCount.textContent = `(${current + 1})`;
                    }
                } else {
                    alert(result.error || 'Ошибка');
                }
            } catch(error) {
                alert('Ошибка соединения');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
});