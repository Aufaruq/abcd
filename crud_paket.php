<?php
session_start();
require_once 'define.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';
$edit = false;
$id = $jenis = $nama_paket = $harga = $id_outlet = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $jenis = $_POST['jenis'] ?? '';
    $nama_paket = $_POST['nama_paket'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $id_outlet = $_POST['id_outlet'] ?? '';
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        $query = "UPDATE tb_paket SET jenis = :jenis, nama_paket = :nama_paket, harga = :harga, id_outlet = :id_outlet WHERE id = :id";
        $message = 'Data paket berhasil diperbarui.';
    } else {
        $query = "INSERT INTO tb_paket (jenis, nama_paket, harga, id_outlet) VALUES (:jenis, :nama_paket, :harga, :id_outlet)";
        $message = 'Data paket berhasil ditambahkan.';
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([':jenis' => $jenis, ':nama_paket' => $nama_paket, ':harga' => $harga, ':id_outlet' => $id_outlet] + (!empty($id) ? [':id' => $id] : []));
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $edit = true;
        $query = "SELECT * FROM tb_paket WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        if ($result) {
            $jenis = $result['jenis'];
            $nama_paket = $result['nama_paket'];
            $harga = $result['harga'];
            $id_outlet = $result['id_outlet'];
        }
    } elseif ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM tb_paket WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $message = 'Data paket berhasil dihapus.';
    }
}

$query = "SELECT p.*, o.nama as nama_outlet FROM tb_paket p JOIN tb_outlet o ON p.id_outlet = o.id";
$stmt = $pdo->query($query);
$pakets = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Produk/Paket Cucian</title>
</head>
<body>
    <h1>CRUD Produk/Paket Cucian</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
        <label for="id_outlet">Outlet:</label><br>
        <select name="id_outlet" id="id_outlet" required>
            <?php
            $outlets = $pdo->query("SELECT * FROM tb_outlet")->fetchAll();
            foreach ($outlets as $outlet) {
                echo "<option value='{$outlet['id']}'" . ($outlet['id'] == $id_outlet ? ' selected' : '') . ">{$outlet['nama']}</option>";
            }
            ?>
        </select><br>
        <label for="jenis">Jenis:</label><br>
        <select id="jenis" name="jenis" required>
            <option value="kiloan" <?= $jenis == 'kiloan' ? 'selected' : '' ?>>Kiloan</option>
            <option value="selimut" <?= $jenis == 'selimut' ? 'selected' : '' ?>>Selimut</option>
            <option value="bed_cover" <?= $jenis == 'bed_cover' ? 'selected' : '' ?>>Bed Cover</option>
            <option value="kaos" <?= $jenis == 'kaos' ? 'selected' : '' ?>>Kaos</option>
            <option value="lain" <?= $jenis == 'lain' ? 'selected' : '' ?>>Lain-lain</option>
        </select><br>
        <label for="nama_paket">Nama Paket:</label><br>
        <input type="text" id="nama_paket" name="nama_paket" value="<?= htmlspecialchars($nama_paket); ?>" required><br>
        <label for="harga">Harga:</label><br>
        <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($harga); ?>" required><br><br>
        <input type="submit" name="save" value="<?= $edit ? 'Update' : 'Simpan' ?>">
    </form>
    <br>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <h2>Daftar Paket Cucian</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Outlet</th>
            <th>Jenis</th>
            <th>Nama Paket</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($pakets as $paket) : ?>
            <tr>
                <td><?= htmlspecialchars($paket['id']); ?></td>
                <td><?= htmlspecialchars($paket['nama_outlet']); ?></td>
                <td><?= htmlspecialchars($paket['jenis']); ?></td>
                <td><?= htmlspecialchars($paket['nama_paket']); ?></td>
                <td><?= htmlspecialchars($paket['harga']); ?></td>
                <td>
                    <a href="?action=edit&id=<?= $paket['id']; ?>">Edit</a> | 
                    <a href="?action=delete&id=<?= $paket['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
