<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сервис Онлайн – B2B платформа аренды недвижимости</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            background: #FFFFFF;
            color: #002633;
            line-height: 1.4;
        }
        /* ШАПКА – тёмно-синяя (#002633) */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #002633;
            z-index: 1000;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        nav {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .logo img {
            height: 45px;
            width: auto;
            display: block;
            /* Убрана фильтрация – лого в исходном свете */
        }
        .nav-links a {
            color: #FFFFFF;
            margin-left: 2rem;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s;
            font-size: 1rem;
        }
        .nav-links a:hover {
            color: #A6C8E1;
        }
        .nav-links .btn-outline {
            border: 1.5px solid #FFFFFF;
            padding: 0.4rem 1.2rem;
            border-radius: 40px;
            background: transparent;
            color: #FFFFFF;
        }
        .nav-links .btn-outline:hover {
            background: #FFFFFF;
            color: #002633;
            border-color: #FFFFFF;
        }
        /* ОСНОВНОЙ КОНТЕНТ */
       main {
            padding-top: 120px;
            padding-left: 20px;
            padding-right: 20px;
            max-width: 1400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        /* КАРТОЧКИ */
        .card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 8px 24px rgba(0,38,51,0.08);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0,38,51,0.12);
        }
        .card-body {
            padding: 1.8rem;
        }
        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: #002633;
        }
        .card-text {
            color: #002633;
            opacity: 0.8;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .card-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #002633;
            margin: 0.5rem 0;
        }
        /* КНОПКИ (базовые) */
        .btn, button, a.btn, input[type="submit"], input[type="button"] {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            background: #A6C8E1;
            color: #002633;
            border: none;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover, button:hover, a.btn:hover {
            background: #A6C8E1;
            color: #002633;
            transform: translateY(-2px);
        }
        /* СВЕТЛАЯ КНОПКА (для главной) */
        .btn-light {
            background: #FFFFFF;
            color: #002633;
        }
        .btn-light:hover {
            background: #A6C8E1;
            color: #002633;
        }
        .btn-outline-light {
            background: transparent;
            border: 2px solid #FFFFFF;
            color: #FFFFFF;
        }
        .btn-outline-light:hover {
            background: #FFFFFF;
            color: #002633;
        }
        /* ФОРМЫ */
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 700;
            color: #002633;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #A6C8E1;
            border-radius: 16px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #002633;
        }
        /* ТАБЛИЦЫ */
        .table-container {
            overflow-x: auto;
            margin: 1.5rem 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #FFFFFF;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,38,51,0.06);
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #A6C8E1;
        }
        th {
            background: #F0F6FA;
            color: #002633;
            font-weight: 700;
        }
        /* АЛЕРТЫ */
        .alert {
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert-success {
            background: #E6F2F7;
            color: #002633;
            border-left: 4px solid #002633;
        }
        .alert-error {
            background: #FFEBEE;
            color: #B00020;
            border-left: 4px solid #B00020;
        }
        .alert-info {
            background: #E6F2F7;
            color: #002633;
            border-left: 4px solid #A6C8E1;
        }
        .alert-warning {
            background: #FFF3E0;
            color: #002633;
            border-left: 4px solid #A6C8E1;
        }
        /* СЕТКА ОБЪЕКТОВ */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        /* ПАГИНАЦИЯ */
        .pagination {
            margin-top: 2rem;
            text-align: center;
        }
        .pagination a {
            margin: 0 4px;
        }
        /* МОДАЛЬНОЕ ОКНО */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,38,51,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 80%;
            margin-top: 5%;
            border-radius: 24px;
        }
        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #FFFFFF;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            padding: 16px;
            color: #FFFFFF;
            font-size: 30px;
            user-select: none;
            background: rgba(0,38,51,0.6);
            border-radius: 50%;
        }
        .prev { left: 20px; }
        .next { right: 20px; }
        .modal-body {
            color: #FFFFFF;
            text-align: center;
            padding: 20px;
            background: rgba(0,38,51,0.8);
            max-width: 600px;
            margin: 0 auto;
            border-radius: 24px;
        }
        /* АДАПТИВ */
        @media (max-width: 768px) {
            nav { flex-direction: column; gap: 1rem; }
            .nav-links a { margin: 0 0.8rem; }
            main { padding-top: 140px; }
            .properties-grid { grid-template-columns: 1fr; }
            .btn, button { padding: 0.6rem 1.2rem; }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="/b2b_rent/">
                    <img src="https://static.tildacdn.com/tild3430-6433-4931-b335-326261316632/Group_81743052.svg" alt="Сервис Онлайн" style="height: 45px;">
                </a>
            </div>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="/b2b_rent/admin/dashboard.php">Панель админа</a>
                        <a href="/b2b_rent/admin/properties.php">Объекты</a>
                        <a href="/b2b_rent/admin/tenants.php">Арендаторы</a>
                        <a href="/b2b_rent/admin/contracts.php">Договоры</a>
                        <a href="/b2b_rent/admin/requests.php">Заявки</a>
                    <?php else: ?>
                        <a href="/b2b_rent/tenant/dashboard.php">Мой кабинет</a>
                        <a href="/b2b_rent/tenant/properties.php">Поиск объектов</a>
                        <a href="/b2b_rent/tenant/my_requests.php">Мои заявки</a>
                    <?php endif; ?>
                    <a href="/b2b_rent/logout.php" class="btn-outline">Выйти</a>
                <?php else: ?>
                    <a href="/b2b_rent/login.php">Вход</a>
                    <a href="/b2b_rent/register.php" class="btn-outline">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>