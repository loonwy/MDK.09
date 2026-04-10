<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
if (isLoggedIn()) header("Location: index.php");
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $company = trim($_POST['company_name']);
    $contact = trim($_POST['contact_person']);
    $phone = trim($_POST['phone']);
    if ($password !== $confirm) $error = "Пароли не совпадают";
    else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $error = "Email уже зарегистрирован";
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, company_name, contact_person, phone, is_profile_completed) VALUES (?, ?, 'tenant', ?, ?, ?, 0)");
            if ($stmt->execute([$email, $hash, $company, $contact, $phone])) {
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = 'tenant';
                $_SESSION['company'] = $company;
                $_SESSION['is_profile_completed'] = 0;
                header("Location: tenant/dashboard.php");
                exit;
            } else $error = "Ошибка регистрации";
        }
    }
}
include 'includes/header.php';
?>
<style>
    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 120px);
    }
    .register-form {
        background: #FFFFFF;
        padding: 2.5rem;
        border-radius: 32px;
        box-shadow: 0 12px 32px rgba(0,38,51,0.1);
        width: 100%;
        max-width: 520px;
        margin: 2rem auto;
    }
    .register-form h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: #002633;
    }
</style>
<div class="register-container">
    <div class="register-form">
        <h2>Регистрация арендатора</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        <form method="post" id="registerForm">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label>Подтверждение пароля:</label>
                <input type="password" name="confirm_password" id="confirm" required>
            </div>
            <div class="form-group">
                <label>Название компании:</label>
                <input type="text" name="company_name" required>
            </div>
            <div class="form-group">
                <label>Контактное лицо:</label>
                <input type="text" name="contact_person" required>
            </div>
            <div class="form-group">
                <label>Телефон:</label>
                <input type="tel" name="phone" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Зарегистрироваться</button>
        </form>
        <p style="text-align: center; margin-top: 1.5rem;">Уже есть аккаунт? <a href="login.php" style="color: #A6C8E1;">Войти</a></p>
    </div>
</div>
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let pwd = document.getElementById('password').value;
        let conf = document.getElementById('confirm').value;
        if (pwd !== conf) {
            alert('Пароли не совпадают');
            e.preventDefault();
        }
    });
</script>
<?php include 'includes/footer.php'; ?>