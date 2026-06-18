<?php
// ============================================================
// koneksi.php — Database connection via PDO
// GlowFinder Beauty — Skincare Recommendation System
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'skincare_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // Ganti jika XAMPP-mu pakai password
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        'error' => true,
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]));
}
