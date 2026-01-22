<?php
require_once '../core/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../index.php");
        exit;
    }
    $error = "Invalid username or password";
}
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="login-page">
    <div class="container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <div class="actions">
                <button type="submit" class="button">Login</button>
            </div>
        </form>
    </div>
</div>