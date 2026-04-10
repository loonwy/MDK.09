document.addEventListener('DOMContentLoaded', function() {
    // Подтверждение удаления
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите удалить?')) {
                e.preventDefault();
            }
        });
    });
});