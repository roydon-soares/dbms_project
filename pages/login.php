<?php
session_start();
require_once '../controllers/authController.php';

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../public/assets/css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>LOGIN</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot?</a>
            </div>
            <button type="submit" name="login">LOGIN</button>
        </form>

        <?php
        // Display error message if set
        if (isset($_SESSION['error_message'])) {
            echo "<p style='color: red; text-align: center;'>{$_SESSION['error_message']}</p>";
            unset($_SESSION['error_message']);
        }
        ?>
    </div>
</body>
</html>