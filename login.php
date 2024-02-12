<?php
session_start();
require_once "define.php";

$message = "Masukkan Username dan password di bawah ini";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($username) && isset($password)) {
        $sql = "SELECT * FROM tb_user WHERE username = :username AND password = :password";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            if (in_array($user['role'], ['admin', 'kasir', 'owner'])) {
                header("Location: dashboard.php");
                exit;
            }
        } else {
            $message = "Username atau password salah.";
        }
    } else {
        $message = "Masukkan username atau password terlebih dahulu.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($message != ""): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="login.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
