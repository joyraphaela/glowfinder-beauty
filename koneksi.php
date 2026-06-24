<?php
// ============================================================
// koneksi.php — Database connection via PDO
// GlowFinder Beauty — Mode: Hardcode Cloud
// ============================================================

$db_host = 'thomas.proxy.rlwy.net'; // Alamat Publik Database Railway
$db_port = '14422';                 // Port Publik Database
$db_name = 'railway';               // Nama database bawaan Railway
$db_user = 'root';                  
$db_pass = 'PKXFfdwzhJWggXDWcrxKPWhwywgehDVb'; // Password aslimu

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
