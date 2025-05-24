<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/User.php';

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Database connection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Hash password and define role
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'admin';

        // Insert admin
        $stmt = $conn->prepare("INSERT INTO admin (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success = "Admin registered successfully. You can now <a href='admin_login.php'>login</a>.";
        } else {
            $error = "Registration failed. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Admin</title>
  <link rel="stylesheet" href="../../assets/css/reg_admin.css">
</head>
<body>
  <header class="landing-header">
    <div class="logo">SmartBank</div>
    <nav>
        <a href="../../public/index.php">Home</a>
      <a href="#features">Features</a>
      <a href="admin_login.php">Admin Login</a>
      <a href="#register_admin.php">Admin Registration</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>
<body>
<div class="login-container">
    <h2>Register Admin Account</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php elseif (!empty($success)): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <button type="submit">Register</button>
    </form>
        <br>
    <p><a href="admin_login.php">Back to Login</a></p>
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