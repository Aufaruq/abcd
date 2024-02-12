<?php
session_start();
require_once 'define.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kasir')) {
    header('Location: login.php');
    exit();
}

$message = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_outlet = $_POST['id_outlet'];
        $kode_invoice = $_POST['kode_invoice'];
        $id_member = $_POST['id_member'];
        $tgl = $_POST['tgl'];
        $batas_waktu = $_POST['batas_waktu'];
        $tgl_bayar = $_POST['tgl_bayar'];
        $biaya_tambahan = $_POST['biaya_tambahan'];
        $diskon = $_POST['diskon'];
        $pajak = $_POST['pajak'];
        $status = $_POST['status'];
        $dibayar = $_POST['dibayar'];
        $id_user = $_SESSION['user_id'];

        $query = "UPDATE tb_transaksi SET id_outlet = :id_outlet, kode_invoice = :kode_invoice, id_member = :id_member, tgl = :tgl, batas_waktu = :batas_waktu, tgl_bayar = :tgl_bayar, biaya_tambahan = :biaya_tambahan, diskon = :diskon, pajak = :pajak, status = :status, dibayar = :dibayar, id_user = :id_user WHERE id = :id";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute([':id_outlet' => $id_outlet, ':kode_invoice' => $kode_invoice, ':id_member' => $id_member, ':tgl' => $tgl, ':batas_waktu' => $batas_waktu, ':tgl_bayar' => $tgl_bayar, ':biaya_tambahan' => $biaya_tambahan, ':diskon' => $diskon, ':pajak' => $pajak, ':status' => $status, ':dibayar' => $dibayar, ':id_user' => $id_user, ':id' => $id])) {
            $message = 'Transaksi berhasil diperbarui.';
            header('Location: crud_transaksi.php');
            exit();
        } else {
            $message = 'Gagal memperbarui transaksi.';
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM tb_transaksi WHERE id = ?");
    $stmt->execute([$id]);
    $transaksi = $stmt->fetch();

    if (!$transaksi) {
        header('Location: crud_transaksi.php');
        exit();
    }
} else {
    header('Location: crud_transaksi.php');
    exit();
}

$members = $pdo->query("SELECT id, nama FROM tb_member")->fetchAll();
$outlets = $pdo->query("SELECT id, nama FROM tb_outlet")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
</head>
<body>
    <h1>Edit Transaksi</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <form action="" method="post">
        <label for="id_outlet">Outlet:</label><br>
        <select name="id_outlet" id="id_outlet" required>
            <?php foreach ($outlets as $outlet) : ?>
                <option value="<?= $outlet['id']; ?>" <?= $outlet['id'] == $transaksi['id_outlet'] ? 'selected' : ''; ?>><?= htmlspecialchars($outlet['nama']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="kode_invoice">Kode Invoice:</label><br>
        <input type="text" id="kode_invoice" name="kode_invoice" value="<?= htmlspecialchars($transaksi['kode_invoice']); ?>" required><br>

        <label for="id_member">Member:</label><br>
        <select name="id_member" id="id_member" required>
            <?php foreach ($members as $member) : ?>
                <option value="<?= $member['id']; ?>" <?= $member['id'] == $transaksi['id_member'] ? 'selected' : ''; ?>><?= htmlspecialchars($member['nama']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="tgl">Tanggal:</label><br>
        <input type="datetime-local" id="tgl" name="tgl" value="<?= date('Y-m-d\TH:i', strtotime($transaksi['tgl'])); ?>" required><br>

        <label for="batas_waktu">Batas Waktu:</label><br>
        <input type="datetime-local" id="batas_waktu" name="batas_waktu" value="<?= date('Y-m-d\TH:i', strtotime($transaksi['batas_waktu'])); ?>" required><br>

        <label for="tgl_bayar">Tanggal Bayar:</label><br>
        <input type="datetime-local" id="tgl_bayar" name="tgl_bayar" value="<?= $transaksi['tgl_bayar'] ? date('Y-m-d\TH:i', strtotime($transaksi['tgl_bayar'])) : ''; ?>"><br>

        <label for="biaya_tambahan">Biaya Tambahan:</label><br>
        <input type="number" id="biaya_tambahan" name="biaya_tambahan" value="<?= $transaksi['biaya_tambahan']; ?>"><br>

        <label for="diskon">Diskon:</label><br>
        <input type="number" id="diskon" name="diskon" value="<?= $transaksi['diskon']; ?>"><br>

        <label for="pajak">Pajak:</label><br>
        <input type="number" id="pajak" name="pajak" value="<?= $transaksi['pajak']; ?>"><br>

        <label for="status">Status:</label><br>
        <select name="status" id="status">
            <option value="baru" <?= $transaksi['status'] == 'baru' ? 'selected' : ''; ?>>Baru</option>
            <option value="proses" <?= $transaksi['status'] == 'proses' ? 'selected' : ''; ?>>Proses</option>
            <option value="selesai" <?= $transaksi['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
            <option value="diambil" <?= $transaksi['status'] == 'diambil' ? 'selected' : ''; ?>>Diambil</option>
        </select><br>

        <label for="dibayar">Dibayar:</label><br>
        <select name="dibayar" id="dibayar">
            <option value="belum_dibayar" <?= $transaksi['dibayar'] == 'belum_dibayar' ? 'selected' : ''; ?>>Belum Dibayar</option>
            <option value="dibayar" <?= $transaksi['dibayar'] == 'dibayar' ? 'selected' : ''; ?>>Dibayar</option>
        </select><br>

        <br>
        <input type="submit" value="Update Transaksi">
    </form>
    <br>
    <a href="crud_transaksi.php">Kembali ke Daftar Transaksi</a>
</body>
</html>
