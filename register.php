<?php
require_once 'config.php';

// Kalau sudah login, langsung ke dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = '';

// Proses form saat di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 9. Sanitasi input dengan htmlspecialchars()
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';

    // 1. Validasi nama tidak kosong
    if (empty($name)) {
        $errors[] = "Nama harus diisi.";
    }

    // 2. Validasi email dengan filter_var()
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    // Validasi password minimal 6 karakter
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    // 5. Cek duplikasi email saat registrasi
    if (empty($errors)) {
        $users = getUsers();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $errors[] = "Email sudah terdaftar. Silakan login.";
                break;
            }
        }
    }

    // Kalau tidak ada error, simpan user baru
    if (empty($errors)) {
        $users = getUsers();

        // 3. Password di-hash dengan password_hash()
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $newUser = [
            'id'       => uniqid('user_', true),
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $users[] = $newUser;

        // 4. Data disimpan di file JSON
        saveUsers($users);

        $success = "Registrasi berhasil! Silakan login.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>📝 Register</h2>

        <!-- 10. Pesan sukses -->
        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <!-- 10. Pesan error -->
        <?php if (!empty($errors)): ?>
            <div class="message error">
                <?php foreach ($errors as $err): ?>
                    <p>⚠️ <?= $err ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Daftar</button>
        </form>

        <div class="link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</body>
</html>