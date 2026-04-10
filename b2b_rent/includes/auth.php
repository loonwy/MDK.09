<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: /b2b_rent/login.php");
        exit;
    }
}

function redirectIfNotAdmin() {
    if (!isAdmin()) {
        header("Location: /b2b_rent/login.php");
        exit;
    }
}
?>