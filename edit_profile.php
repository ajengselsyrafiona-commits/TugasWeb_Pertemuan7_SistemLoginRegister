<?php
require_once 'config.php';
requireLogin();

$errors = [];
$success = '';

// Ambil data user saat ini
$users = getUsers();
$currentUser = null;
$userIndex = null;

foreach ($users as $index => $user) {
    if ($user['id'] === $_SESSION['user_id']) {
        $currentUser = $user;
        $userIndex = $index;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (empty($name)) {
        $errors[] = "Nama harus diisi.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    // Cek duplikasi email (kecuali email sendiri)
    if (empty($errors)) {
        foreach ($users as $user) {
            if ($user['email'] === $email && $user['id'] !== $_SESSION['user_id']) {
                $errors[] = "Email sudah digunakan user lain.";
                break;
            }
        }
    }

    // Update password jika diisi
    $newPassword = $_POST['new_password'] ?? '';
    if (!empty($newPassword) && strlen($newPassword) < 6) {
        $errors[] = "Password baru minimal 6 karakter.";
    }

    if (empty($errors)) {
        $users[$userIndex]['name']  = $name;
        $users[$userIndex]['email'] = $email;

        if (!empty($newPassword)) {
            $users[$userIndex]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        saveUsers($users);

        // Update session juga
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;

        $success = "Profile berhasil diperbarui!";

        // Refresh data
        $currentUser = $users[$userIndex];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>✏️ Edit Profile</h2>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

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
                       value="<?= htmlspecialchars($currentUser['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($currentUser['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="new_password">Password Baru (kosongkan jika tidak ganti)</label>
                <input type="password" id="new_password" name="new_password">
            </div>

            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="dashboard.php">
                <button type="button" class="btn btn-secondary">Kembali</button>
            </a>
        </form>
    </div>
</body>
</html>