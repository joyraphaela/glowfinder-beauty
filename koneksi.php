<?php
// ============================================================
// koneksi.php — Database connection via PDO
// GlowFinder Beauty — Mode: Auto-Detect Environment (Railway)
// ============================================================

// Sistem akan otomatis mengambil data dari tab Variables di Railway
$db_host = getenv('MYSQLHOST')     ?: 'localhost';
$db_port = getenv('MYSQLPORT')     ?: '3306';
$db_name = getenv('MYSQLDATABASE') ?: 'skincare_db';
$db_user = getenv('MYSQLUSER')     ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: '';

$dsn = "mysql:host=" . $db_host . ";port=" . $db_port . ";dbname=" . $db_name . ";charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        'error' => true,
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]));
}
?>
