<?php
session_start();
require_once 'define.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$message = '';
$edit = false;
$id = $nama = $username = $password = $role = $id_outlet = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $id_outlet = $_POST['id_outlet'] ?? '';

    if ($id) { 
        if (!empty($password)) {
            $query = "UPDATE tb_user SET nama = :nama, username = :username, password = :password, role = :role, id_outlet = :id_outlet WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':nama' => $nama, ':username' => $username, ':password' => $password, ':role' => $role, ':id_outlet' => $id_outlet, ':id' => $id]);
        } else {
            $query = "UPDATE tb_user SET nama = :nama, username = :username, role = :role, id_outlet = :id_outlet WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':nama' => $nama, ':username' => $username, ':role' => $role, ':id_outlet' => $id_outlet, ':id' => $id]);
        }
        $message = 'Data pengguna berhasil diperbarui.';
    } else { 
        $query = "INSERT INTO tb_user (nama, username, password, role, id_outlet) VALUES (:nama, :username, :password, :role, :id_outlet)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':nama' => $nama, ':username' => $username, ':password' => $password, ':role' => $role, ':id_outlet' => $id_outlet]);
        $message = 'Data pengguna berhasil ditambahkan.';
    }
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $edit = true;
        $query = "SELECT * FROM tb_user WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        if ($user) {
            $nama = $user['nama'];
            $username = $user['username'];
            $role = $user['role'];
            $id_outlet = $user['id_outlet'];
        }
    } elseif ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = "DELETE FROM tb_user WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $message = 'Data pengguna berhasil dihapus.';
    }
}

$query = "SELECT u.*, o.nama as nama_outlet FROM tb_user u LEFT JOIN tb_outlet o ON u.id_outlet = o.id";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Pengguna</title>
    <!-- Sisipkan CSS eksternal atau internal untuk mempercantik tampilan jika perlu -->
</head>
<body>
    <h1>CRUD Pengguna</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama); ?>" required><br>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username); ?>" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" <?= !$edit ? 'required' : ''; ?>><br>
        <small><?= $edit ? 'Biarkan kosong jika tidak ingin mengubah password' : ''; ?></small><br>
        <label for="role">Role:</label><br>
        <select id="role" name="role" required>
            <option value="admin" <?= $role == 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="kasir" <?= $role == 'kasir' ? 'selected' : ''; ?>>Kasir</option>
            <option value="owner" <?= $role == 'owner' ? 'selected' : ''; ?>>Owner</option>
        </select><br>
        <label for="id_outlet">Outlet:</label><br>
        <select name="id_outlet" id="id_outlet" required>
            <?php
            $outlets = $pdo->query("SELECT * FROM tb_outlet")->fetchAll();
            foreach ($outlets as $outlet) {
                echo "<option value='{$outlet['id']}'" . ($outlet['id'] == $id_outlet ? 'selected' : '') . ">{$outlet['nama']}</option>";
            }
            ?>
        </select><br><br>
        <input type="submit" value="<?= $edit ? 'Update' : 'Simpan' ?>">
    </form>
    <br>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <h2>Daftar Pengguna</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th>Outlet</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?= htmlspecialchars($user['id']); ?></td>
                <td><?= htmlspecialchars($user['nama']); ?></td>
                <td><?= htmlspecialchars($user['username']); ?></td>
                <td><?= htmlspecialchars($user['role']); ?></td>
                <td><?= htmlspecialchars($user['nama_outlet']); ?></td>
                <td>
                    <a href="?action=edit&id=<?= $user['id']; ?>">Edit</a> | 
                    <a href="?action=delete&id=<?= $user['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

