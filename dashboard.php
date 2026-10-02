<?php
require_once 'config.php';

// 7. Dashboard yang diproteksi (redirect jika belum login)
requireLogin();

// Ambil data user terbaru dari JSON
$users = getUsers();
$currentUser = null;
foreach ($users as $user) {
    if ($user['id'] === $_SESSION['user_id']) {
        $currentUser = $user;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 550px;">
        <div class="dashboard-header">
            <h2>🏠 Dashboard</h2>
            <a href="logout.php" style="color:#f472b6; text-decoration:none;">Logout</a>
        </div>

        <div class="message success">
            ✅ Selamat datang, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!
        </div>

        <div class="user-info">
            <p><strong>Nama:</strong> <?= htmlspecialchars($currentUser['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($currentUser['email']) ?></p>
            <p><strong>Terdaftar:</strong> <?= htmlspecialchars($currentUser['created_at']) ?></p>
        </div>

        <a href="edit_profile.php">
            <button class="btn">✏️ Edit Profile</button>
        </a>
        <a href="logout.php">
            <button class="btn btn-secondary">🚪 Logout</button>
        </a>
    </div>
</body>
</html>