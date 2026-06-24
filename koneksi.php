<?php
// ============================================================
// koneksi.php — Database connection via PDO
// GlowFinder Beauty — Mode: Absolute Internal Hardcode IPv6
// ============================================================

$db_host = 'mysql.railway.internal'; // Host internal resmi Railway
$db_port = '3306';                   // Port internal standar
$db_name = 'railway';                // Nama database bawaan cloud
$db_user = 'root';                  
$db_pass = 'PKXFfdwzhJWggXDWcrxKPWhwywgehDVb'; // Password asli database kamu

// Trik Khusus: Paksa PDO menggunakan protokol TCP/IP murni untuk menghindari socket local 'No such file'
$dsn = "mysql:host=" . $db_host . ";port=" . $db_port . ";dbname=" . $db_name . ";charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Baris di bawah ini adalah kunci utama untuk memaksa koneksi jaringan cloud murni
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
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
