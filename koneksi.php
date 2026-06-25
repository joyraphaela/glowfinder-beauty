<?php
// ============================================================
// koneksi.php — Database connection via PDO
// GlowFinder Beauty — Mode: Absolute Hardcode Custom User
// ============================================================

$db_host = 'mysql.railway.internal'; // Jalur internal IPv6 cloud Railway
$db_port = '3306';                   // Port internal standar
$db_name = 'railway';                // Nama database di cloud
$db_user = 'glowuser';               // Memaksa login menggunakan user baru yang kamu buat
$db_pass = 'PKXFfdwzhJWggXDWcrxKPWhwywgehDVb'; // Password asli database kamu

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
