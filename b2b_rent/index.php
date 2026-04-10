<?php
require_once 'includes/config.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') header("Location: admin/dashboard.php");
    else header("Location: tenant/dashboard.php");
    exit;
}
include 'includes/header.php';
?>
<style>
    main {
    padding: 0 !important;
    max-width: 100% !important;
}
</style>

<section class="hero" style="background: linear-gradient(135deg, rgba(0,38,51,0.4), rgba(0,38,51,0.4)), url('https://static.tildacdn.com/tild3533-3036-4566-b730-613962393665/daylight-analog-city.png') center/cover no-repeat; padding: 160px 0 120px; color: #FFFFFF; text-align: center;">
    <div style="max-width: 900px; margin: 0 auto; padding: 0 20px;">
        <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: 1rem;">Сервис Онлайн</h1>
        <p style="font-size: 1.4rem; margin-bottom: 2rem;">Цифровая B2B-платформа для управления арендой коммерческой недвижимости</p>
        <p style="font-size: 1.2rem; margin-bottom: 2.5rem;">Автоматизация учёта объектов, договоров, платежей и заявок. Для управляющих компаний и арендаторов.</p>
        <div>
            <a href="register.php" class="btn btn-light" style="background: #FFFFFF; color: #002633; font-size: 1.1rem; padding: 12px 32px; margin: 0 10px;">Начать работу</a>
            <a href="#services" class="btn btn-outline-light" style="font-size: 1.1rem; padding: 12px 32px;">Узнать больше</a>
        </div>
    </div>
</section>

<section style="padding: 80px 0; background: #FFFFFF;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
        <h2 style="font-size: 2.2rem; color: #002633; margin-bottom: 1rem;">О компании «Сервис Онлайн»</h2>
        <div style="width: 80px; height: 3px; background: #002633; margin: 0 auto 2rem;"></div>
        <p style="font-size: 1.1rem; max-width: 800px; margin: 0 auto; color: #002633;">Мы предоставляем современные цифровые решения для управления коммерческой недвижимостью. Наша платформа помогает арендодателям и арендаторам взаимодействовать быстро, прозрачно и безопасно.</p>
    </div>
</section>

<section id="services" style="padding: 80px 0; background: #F7F9FC;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2 style="text-align: center; font-size: 2.2rem; color: #002633; margin-bottom: 3rem;">Наши услуги</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            <div class="card" style="flex: 1; min-width: 280px;"><div class="card-body" style="text-align: center;"><div style="font-size: 3rem; margin-bottom: 1rem;">🏢</div><h3 class="card-title">Управление объектами</h3><p class="card-text">Добавляйте, редактируйте и отслеживайте статус помещений. Загружайте фотографии, указывайте площадь и ставку.</p></div></div>
            <div class="card" style="flex: 1; min-width: 280px;"><div class="card-body" style="text-align: center;"><div style="font-size: 3rem; margin-bottom: 1rem;">📄</div><h3 class="card-title">Договоры и платежи</h3><p class="card-text">Автоматический расчёт арендной платы, учёт оплат, контроль задолженности. Онлайн‑отчёты.</p></div></div>
            <div class="card" style="flex: 1; min-width: 280px;"><div class="card-body" style="text-align: center;"><div style="font-size: 3rem; margin-bottom: 1rem;">🤝</div><h3 class="card-title">Заявки на аренду</h3><p class="card-text">Арендаторы отправляют заявки, администратор одобряет или отклоняет. Полный цикл согласования.</p></div></div>
            <div class="card" style="flex: 1; min-width: 280px;"><div class="card-body" style="text-align: center;"><div style="font-size: 3rem; margin-bottom: 1rem;">📊</div><h3 class="card-title">Аналитика и отчёты</h3><p class="card-text">Формируйте отчёты по объектам, арендаторам, платежам для принятия решений.</p></div></div>
        </div>
    </div>
</section>

<section style="padding: 80px 0; background: #002633; color: #FFFFFF;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 3rem; color: #FFFFFF;">Почему выбирают нас</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-around;">
            <div><div style="font-size: 2.5rem; font-weight: 700;">100+</div><div>объектов в управлении</div></div>
            <div><div style="font-size: 2.5rem; font-weight: 700;">98%</div><div>довольных клиентов</div></div>
            <div><div style="font-size: 2.5rem; font-weight: 700;">24/7</div><div>техподдержка</div></div>
            <div><div style="font-size: 2.5rem; font-weight: 700;">5 лет</div><div>на рынке</div></div>
        </div>
    </div>
</section>

<section style="padding: 80px 0; background: #FFFFFF;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2 style="text-align: center; font-size: 2rem; color: #002633; margin-bottom: 3rem;">Как начать работать с платформой</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <div style="flex: 1; text-align: center;"><div style="background: #A6C8E1; width: 60px; height: 60px; line-height: 60px; border-radius: 60px; font-size: 1.5rem; font-weight: bold; color: #002633; margin: 0 auto 1rem;">1</div><h3 style="color: #002633;">Регистрация</h3><p style="color: #002633;">Создайте аккаунт арендатора или администратора</p></div>
            <div style="flex: 1; text-align: center;"><div style="background: #A6C8E1; width: 60px; height: 60px; line-height: 60px; border-radius: 60px; font-size: 1.5rem; font-weight: bold; color: #002633; margin: 0 auto 1rem;">2</div><h3 style="color: #002633;">Заполните профиль</h3><p style="color: #002633;">Укажите реквизиты компании для подачи заявок</p></div>
            <div style="flex: 1; text-align: center;"><div style="background: #A6C8E1; width: 60px; height: 60px; line-height: 60px; border-radius: 60px; font-size: 1.5rem; font-weight: bold; color: #002633; margin: 0 auto 1rem;">3</div><h3 style="color: #002633;">Выберите объект</h3><p style="color: #002633;">Подайте заявку на аренду и получите одобрение</p></div>
        </div>
    </div>
</section>

<section style="padding: 80px 0; background: #F7F9FC; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-size: 2rem; color: #002633; margin-bottom: 1rem;">Готовы оптимизировать управление арендой?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem; color: #002633;">Присоединяйтесь к «Сервис Онлайн» уже сегодня</p>
        <a href="register.php" class="btn btn-light" style="background: #FFFFFF; color: #002633; padding: 12px 36px;">Зарегистрироваться</a>
        <a href="login.php" class="btn" style="background: #A6C8E1; color: #002633; margin-left: 15px;">Войти</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>