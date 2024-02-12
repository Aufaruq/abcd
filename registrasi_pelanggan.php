<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userRole = $_SESSION['role'];
if ($userRole === 'owner') {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'define.php';

    $nama = $_POST['nama'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tlp = $_POST['tlp'] ?? '';

    $query = "INSERT INTO tb_member (nama, alamat, jenis_kelamin, tlp) VALUES (:nama, :alamat, :jenis_kelamin, :tlp)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':nama' => $nama,
        ':alamat' => $alamat,
        ':jenis_kelamin' => $jenis_kelamin,
        ':tlp' => $tlp,
    ]);

    $pesanSukses = "Pelanggan berhasil didaftarkan.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Pelanggan</title>
</head>
<body>
    <h1>Registrasi Pelanggan</h1>
    <?php if (!empty($pesanSukses)) echo "<p>$pesanSukses</p>"; ?>
    <form action="registrasi_pelanggan.php" method="post">
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" required><br>
        <label for="alamat">Alamat:</label><br>
        <textarea id="alamat" name="alamat" required></textarea><br>
        <label for="jenis_kelamin">Jenis Kelamin:</label><br>
        <select id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select><br>
        <label for="tlp">Telepon:</label><br>
        <input type="text" id="tlp" name="tlp" required><br><br>
        <input type="submit" value="Daftar">
    </form>
    <button onclick="window.location.href='dashboard.php';">Kembali ke Dashboard</button>
</body>
</html>
