<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pdm');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Tidak bisa terkoneksi ke database. Error: " . $e->getMessage());
}
?>
