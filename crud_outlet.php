<?php
session_start();
require_once 'define.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $tlp = $_POST['tlp'] ?? '';
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        $query = "UPDATE tb_outlet SET nama = :nama, alamat = :alamat, tlp = :tlp WHERE id = :id";
        $message = 'Data outlet berhasil diperbarui.';
    } else {
        $query = "INSERT INTO tb_outlet (nama, alamat, tlp) VALUES (:nama, :alamat, :tlp)";
        $message = 'Data outlet berhasil ditambahkan.';
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([':nama' => $nama, ':alamat' => $alamat, ':tlp' => $tlp] + (!empty($id) ? [':id' => $id] : []));
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM tb_outlet WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $message = 'Data outlet berhasil dihapus.';
}

$nama = $alamat = $tlp = $id = '';
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM tb_outlet WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $outlet = $stmt->fetch();

    if ($outlet) {
        $nama = $outlet['nama'];
        $alamat = $outlet['alamat'];
        $tlp = $outlet['tlp'];
        $id = $outlet['id'];
    }
}

$query = "SELECT * FROM tb_outlet";
$stmt = $pdo->query($query);
$outlets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Outlet</title>
</head>
<body>
    <h1>CRUD Outlet</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <form action="crud_outlet.php" method="post">
        <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($id) ?>">
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($nama) ?>"><br>
        <label for="alamat">Alamat:</label><br>
        <textarea id="alamat" name="alamat" required><?= htmlspecialchars($alamat) ?></textarea><br>
        <label for="tlp">Telepon:</label><br>
        <input type="text" id="tlp" name="tlp" required value="<?= htmlspecialchars($tlp) ?>"><br><br>
        <input type="submit" value="Simpan">
    </form>
    <h2>Daftar Outlet</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($outlets as $outlet): ?>
            <tr>
                <td><?= htmlspecialchars($outlet['nama']) ?></td>
                <td><?= htmlspecialchars($outlet['alamat']) ?></td>
                <td><?= htmlspecialchars($outlet['tlp']) ?></td>
                <td>
                    <a href="?action=edit&id=<?= $outlet['id'] ?>">Edit</a> |
                    <a href="?action=delete&id=<?= $outlet['id'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='dashboard.php';">Kembali ke Dashboard</button>
</body>
</html>
