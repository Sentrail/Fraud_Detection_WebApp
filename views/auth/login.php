<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $user = new User($db);
    $account_number = $user->login($username, $password);
    
    if ($account_number) {
        // Fetch the full user record to get the 'id'
        $user_data = $user->getUserById($account_number);
        if ($user_data) {
            $_SESSION['user_id'] = $user_data['id']; // Set user_id from the users table
            $_SESSION['account_number'] = $account_number;
            $_SESSION['username'] = $username;
            header("Location: ../dashboard/index.php");
            exit();
        } else {
            $_SESSION['error'] = "User data not found.";
        }
    } else {
        $_SESSION['error'] = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <link rel="stylesheet" href="../../assets/css/user_login.css">
</head>
<body>
    <header class="landing-header">
        <div class="logo">SmartBank</div>
        <nav>
            <a href="../../public/index.php">Home</a>
            <a href="#features">Features</a>
            <a href="admin_login.php">Admin Login</a>
            <a href="register_admin.php">Admin Registration</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>
    <h2>Customer Login</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required
               value="<?= isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : '' ?>">
        <input type="password" name="password" placeholder="Password" id="password" required>
        <div class="checkbox-container">
            <label><input type="checkbox" onclick="togglePassword()"> Show Password</label>
        </div>
        <div class="checkbox-container">
            <label><input type="checkbox" name="remember"> Remember Me</label>
        </div>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            passwordField.type = passwordField.type === "password" ? "text" : "password";
        }
    </script>
    <br><br>
    <section id="contact" class="contact">
        <h2>Contact Us</h2>
        <p>Email: support@smartbank.com | Phone: +234-xxx-xxx-xxxx</p>
    </section>
    <footer>
        <p>&copy; <?= date('Y') ?> SmartBank. All rights reserved.</p>
    </footer>
</body>
</html>