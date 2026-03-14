<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой блог</title>
    <link rel="stylesheet" href="/blog/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="/blog/index.php" class="logo">Мой Блог</a>
                <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="/blog/index.php">Главная</a></li>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="welcome-message">Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                        <li><a href="/blog/user/index.php" class="user-link">👤 Мой кабинет</a></li>
                        
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li><a href="/blog/admin/index.php" class="admin-link">📋 Админ-панель</a></li>
                        <?php endif; ?>
                        
                        <li><a href="/blog/logout.php">Выйти</a></li>
                    <?php else: ?>
                        <li><a href="/blog/login.php">Вход</a></li>
                        <li><a href="/blog/register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container">