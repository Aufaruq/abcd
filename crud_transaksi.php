<?php
session_start();
require_once 'define.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM tb_transaksi WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$transaksi = $pdo->query("SELECT * FROM tb_transaksi")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi</title>
</head>
<body>
    <h1>Daftar Transaksi</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Kode Invoice</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Dibayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transaksi as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['kode_invoice']) ?></td>
                    <td><?= htmlspecialchars($t['tgl']) ?></td>
                    <td><?= htmlspecialchars($t['status']) ?></td>
                    <td><?= htmlspecialchars($t['dibayar']) ?></td>
                    <td>
                        <a href="edit_transaksi.php?id=<?= $t['id'] ?>">Edit</a> | 
                        <a href="?aksi=hapus&id=<?= $t['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="entri_transaksi.php">Kembali ke entri transaksi</a>
</body>
</html>
