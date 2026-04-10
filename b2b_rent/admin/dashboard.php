<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();
?>
<?php include '../includes/header.php'; ?>
<h1>Панель администратора</h1>
<div class="properties-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
    <a href="properties.php" class="card" style="text-decoration: none; color: inherit;">
        <div class="card-body">
            <h3>Управление объектами</h3>
            <p>Добавление, редактирование, удаление объектов недвижимости</p>
        </div>
    </a>
    <a href="tenants.php" class="card" style="text-decoration: none; color: inherit;">
        <div class="card-body">
            <h3>Арендаторы</h3>
            <p>Управление арендаторами</p>
        </div>
    </a>
    <a href="contracts.php" class="card" style="text-decoration: none; color: inherit;">
        <div class="card-body">
            <h3>Договоры</h3>
            <p>Просмотр и управление договорами</p>
        </div>
    </a>
    <a href="payments.php" class="card" style="text-decoration: none; color: inherit;">
        <div class="card-body">
            <h3>Платежи</h3>
            <p>Учёт платежей</p>
        </div>
    </a>
    <a href="requests.php" class="card" style="text-decoration: none; color: inherit;">
        <div class="card-body">
            <h3>Заявки на аренду</h3>
            <p>Одобрение/отклонение заявок</p>
        </div>
    </a>
</div>
<?php include '../includes/footer.php'; ?>