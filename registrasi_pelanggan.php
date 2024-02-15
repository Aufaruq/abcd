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

require_once 'define.php';

$pesanSukses = "";
$isEditing = false;
$editId = null;

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $isEditing = true;
    $editId = $_GET['id'];
    $editQuery = "SELECT * FROM tb_member WHERE id = :id LIMIT 1";
    $editStmt = $pdo->prepare($editQuery);
    $editStmt->execute([':id' => $editId]);
    $editData = $editStmt->fetch(PDO::FETCH_ASSOC);

    if (!$editData) {
        $isEditing = false;
        $pesanSukses = "Data tidak ditemukan.";
    }
}
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $deleteId = $_GET['id'];
    $deleteQuery = "DELETE FROM tb_member WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteExecuted = $deleteStmt->execute([':id' => $deleteId]);

    if ($deleteExecuted) {
        $pesanSukses = "Pelanggan berhasil dihapus.";
        header('Location: registrasi_pelanggan.php');
        exit();
    } else {
        $pesanSukses = "Gagal menghapus pelanggan.";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tlp = $_POST['tlp'] ?? '';
    $editId = $_POST['edit_id'] ?? null;

    $params = [
        ':nama' => $nama,
        ':alamat' => $alamat,
        ':jenis_kelamin' => $jenis_kelamin,
        ':tlp' => $tlp,
    ];

    if ($editId) {
        $query = "UPDATE tb_member SET nama = :nama, alamat = :alamat, jenis_kelamin = :jenis_kelamin, tlp = :tlp WHERE id = :id";
        $params[':id'] = $editId;
        $pesanSukses = "Pelanggan berhasil diperbarui.";
    } else {
        $query = "INSERT INTO tb_member (nama, alamat, jenis_kelamin, tlp) VALUES (:nama, :alamat, :jenis_kelamin, :tlp)";
        $pesanSukses = "Pelanggan berhasil didaftarkan.";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
}

$members = [];
$query = "SELECT * FROM tb_member";
$stmt = $pdo->prepare($query);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Pelanggan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4"><?= $isEditing ? 'Edit Pelanggan' : 'Registrasi Pelanggan' ?></h1>
        <?php if (!empty($pesanSukses)) : ?>
            <div class="alert alert-success" role="alert">
                <?= $pesanSukses; ?>
            </div>
        <?php endif; ?>
        <form action="registrasi_pelanggan.php" method="post" class="mb-4">
            <?php if ($isEditing) : ?>
                <input type="hidden" name="edit_id" value="<?= $editData['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" class="form-control" value="<?= $isEditing ? htmlspecialchars($editData['nama']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" class="form-control" required><?= $isEditing ? htmlspecialchars($editData['alamat']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                    <option value="L" <?= $isEditing && $editData['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?= $isEditing && $editData['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tlp">Telepon:</label>
                <input type="text" id="tlp" name="tlp" class="form-control" value="<?= $isEditing ? htmlspecialchars($editData['tlp']) : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $isEditing ? 'Update' : 'Daftar' ?></button>
        </form>
        <button onclick="window.location.href='dashboard.php';" class="btn btn-secondary mb-3">Kembali ke Dashboard</button>
        <table border="1" class="table table-striped">
        <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Jenis Kelamin</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member) : ?>
                    <tr>
                        <td><?= htmlspecialchars($member['id']); ?></td>
                        <td><?= htmlspecialchars($member['nama']); ?></td>
                        <td><?= htmlspecialchars($member['alamat']); ?></td>
                        <td><?= htmlspecialchars($member['jenis_kelamin']); ?></td>
                        <td><?= htmlspecialchars($member['tlp']); ?></td>
                        <td>
                            <a href="?action=edit&id=<?= $member['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                            <a href="?action=delete&id=<?= $member['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')"><i class="fas fa-trash-alt"></i> Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
