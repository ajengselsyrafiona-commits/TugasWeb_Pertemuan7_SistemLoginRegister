<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    if (empty($password)) {
        $errors[] = "Password harus diisi.";
    }

    if (empty($errors)) {
        $users = getUsers();
        $foundUser = null;

        // Cari user berdasarkan email
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $foundUser = $user;
                break;
            }
        }

        // 6. Sistem login dengan session + verifikasi password
        if ($foundUser && password_verify($password, $foundUser['password'])) {

            // Simpan data user ke session
            $_SESSION['user_id']   = $foundUser['id'];
            $_SESSION['user_name'] = $foundUser['name'];
            $_SESSION['user_email'] = $foundUser['email'];

            // Bonus: Remember Me dengan cookies (30 hari)
            if ($remember) {
                setcookie('remember_email', $email, time() + (86400 * 30), "/");
                setcookie('remember_token', $foundUser['id'], time() + (86400 * 30), "/");
            } else {
                // Hapus cookie kalau tidak dicentang
                if (isset($_COOKIE['remember_email'])) {
                    setcookie('remember_email', '', time() - 3600, "/");
                    setcookie('remember_token', '', time() - 3600, "/");
                }
            }

            redirect('dashboard.php');
        } else {
            $errors[] = "Email atau password salah.";
        }
    }
}

// Auto-fill email dari cookie Remember Me
$savedEmail = $_COOKIE['remember_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>🔐 Login</h2>

        <?php if (!empty($errors)): ?>
            <div class="message error">
                <?php foreach ($errors as $err): ?>
                    <p>⚠️ <?= $err ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($savedEmail) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Bonus: Remember Me -->
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember"
                       <?= $savedEmail ? 'checked' : '' ?>>
                <label for="remember">Ingat saya (30 hari)</label>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <div class="link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>