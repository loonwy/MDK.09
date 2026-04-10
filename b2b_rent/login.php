<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
if (isLoggedIn()) {
    if ($_SESSION['role'] == 'admin') header("Location: admin/dashboard.php");
    else header("Location: tenant/dashboard.php");
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['company'] = $user['company_name'];
        $_SESSION['is_profile_completed'] = $user['is_profile_completed'] ?? 0;
        if ($user['role'] == 'admin') header("Location: admin/dashboard.php");
        else header("Location: tenant/dashboard.php");
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
include 'includes/header.php';
?>
<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 120px);
    }
    .login-form {
        background: #FFFFFF;
        padding: 2.5rem;
        border-radius: 32px;
        box-shadow: 0 12px 32px rgba(0,38,51,0.1);
        width: 100%;
        max-width: 480px;
        margin: 2rem auto;
    }
    .login-form h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: #002633;
    }
</style>
<div class="login-container">
    <div class="login-form">
        <h2>Вход в систему</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Войти</button>
        </form>
        <p style="text-align: center; margin-top: 1.5rem;">Нет аккаунта? <a href="register.php" style="color: #A6C8E1;">Зарегистрироваться</a></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>