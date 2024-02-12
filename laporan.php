<?php
require_once 'define.php';

session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'kasir', 'owner'])) {
    header('Location: login.php');
    exit();
}

try {
    $query = "SELECT id, kode_invoice, tgl, status, dibayar FROM tb_transaksi";
    $stmt = $pdo->query($query);
    $transaksi = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data transaksi: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
</head>
<body>
    <h1>Laporan Semua Transaksi</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Invoice</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Dibayar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transaksi as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['id']) ?></td>
                    <td><?= htmlspecialchars($t['kode_invoice']) ?></td>
                    <td><?= htmlspecialchars($t['tgl']) ?></td>
                    <td><?= htmlspecialchars($t['status']) ?></td>
                    <td><?= htmlspecialchars($t['dibayar']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php"><button>Kembali ke Dashboard</button></a>
</body>
</html>

