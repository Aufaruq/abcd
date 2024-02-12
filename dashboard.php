<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$userRole = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Aplikasi Laundry</h1>
    <p>Selamat datang, kamu adalah <strong><?php echo htmlspecialchars($userRole); ?></strong>.</p>
    <ul>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="registrasi_pelanggan.php">Registrasi Pelanggan</a></li>
        <li><a href="crud_outlet.php">CRUD Outlet</a></li>
        <li><a href="crud_paket.php">CRUD Produk/Paket Cucian</a></li>
        <li><a href="crud_pengguna.php">CRUD Pengguna</a></li>
        <li><a href="entri_transaksi.php">Entri Transaksi</a></li>
        <li><a href="laporan.php">Generate Laporan</a></li>
    </ul>
</body>
</html>
