<?php
session_start();
require_once "../../views/templates/header.php";
require_once '../../config/database.php';
require_once '../../models/User.php';

$error = "";

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $userModel = new User($db);
    $loggedInAdmin = $userModel->loginAdmin($username, $password); // Use loginAdmin instead of login

    if ($loggedInAdmin) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: ../../views/admin/admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="../../assets/css/reg_admin.css">
</head>
<body>
  <header class="landing-header">
    <div class="logo">SmartBank</div>
    <nav>
      <a href="../../public/index.php">Home</a>
      <a href="#features">Features</a>
      <a href="#admin_login.php">Admin Login</a>
      <a href="register_admin.php">Admin Registration</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>
<body>
<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

    <p><a href="../../public/index.php">Back to Home</a></p>
</div>
<br>
<section id="contact" class="contact">
    <h2>Contact Us</h2>
    <p>Email: support@smartbank.com | Phone: +234-xxx-xxx-xxxx</p>
  </section>

  <footer>
    <p>&copy; <?= date('Y') ?> SmartBank. All rights reserved.</p>
  </footer>
</body>
</html>