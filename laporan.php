<?php
session_start();
require_once 'define.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'kasir', 'owner'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_awal = $_POST['tanggal_awal'];
    $tanggal_akhir = $_POST['tanggal_akhir'];

    $sql = "SELECT * FROM tb_transaksi WHERE tgl BETWEEN :tanggal_awal AND :tanggal_akhir";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':tanggal_awal' => $tanggal_awal, ':tanggal_akhir' => $tanggal_akhir]);
    $transaksi = $stmt->fetchAll();

    echo "<h1>Laporan Transaksi</h1>";
    echo "<h2>Rentang Tanggal: $tanggal_awal - $tanggal_akhir</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Kode Invoice</th><th>Tanggal</th><th>Status</th><th>Dibayar</th></tr>";
    foreach ($transaksi as $t) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($t['kode_invoice']) . "</td>";
        echo "<td>" . htmlspecialchars($t['tgl']) . "</td>";
        echo "<td>" . htmlspecialchars($t['status']) . "</td>";
        echo "<td>" . htmlspecialchars($t['dibayar']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_transaksi.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Kode Invoice', 'Tanggal', 'Status', 'Dibayar']);
    foreach ($transaksi as $t) {
        fputcsv($output, [$t['kode_invoice'], $t['tgl'], $t['status'], $t['dibayar']]);
    }
    fclose($output);
} else {
    header('Location: laporan.php');
    exit();
}
?>
