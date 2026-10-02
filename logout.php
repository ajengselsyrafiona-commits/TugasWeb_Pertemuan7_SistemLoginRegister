<?php
require_once 'config.php';

// 8. Logout functionality (session_destroy)
$_SESSION = []; // Kosongkan semua data session

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Hapus juga cookie remember me
setcookie('remember_email', '', time() - 3600, "/");
setcookie('remember_token', '', time() - 3600, "/");

redirect('login.php');
?>