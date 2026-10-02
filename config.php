<?php
// Mulai session di setiap halaman yang butuh login
session_start();

// Path file JSON untuk simpan data user
define('DATA_FILE', __DIR__ . '/data/users.json');

// Pastikan folder data ada
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}

// Pastikan file JSON ada, kalau belum buat dengan array kosong
if (!file_exists(DATA_FILE)) {
    file_put_contents(DATA_FILE, json_encode([]));
}

/**
 * Ambil semua user dari file JSON
 */
function getUsers() {
    $data = file_get_contents(DATA_FILE);
    return json_decode($data, true) ?? [];
}

/**
 * Simpan array user ke file JSON
 */
function saveUsers($users) {
    file_put_contents(DATA_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

/**
 * Redirect ke halaman tertentu
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Wajibkan login — kalau belum, lempar ke login.php
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}
?>