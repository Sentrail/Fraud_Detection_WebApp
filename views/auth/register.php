<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../../config/database.php";
    require_once "../../models/User.php";

    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $user = new User($db);

    // Capture Form Data
    $name = $_POST['name'];
    $dob = date('Y-m-d', strtotime($_POST['dob'])); // Convert input to correct MySQL format New Date of Birth Field
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $bank_branch = $_POST['bank_branch'];
    $account_type = $_POST['account_type'];
    $transaction_location = $_POST['transaction_location'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';


    // Process Image Upload
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
    $profile_picture = basename($_FILES["profile_picture"]["name"]);

    if (empty($username)) {
        echo "Error: Username is required!";
        exit(); // Stop execution if username is empty
    }    

    if ($user->register($username, $name, $dob, $gender, $state, $city, $bank_branch, $account_type, 
    $transaction_location, $contact, $email, $security_question, 
    $security_answer, $address, $profile_picture, $password)) {
        $_SESSION['message'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed. Try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SmartBank - Secure Savings</title>
 <link rel="stylesheet" href="../../assets/css/user_reg.css">
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
<body>
    <h2>User Registration</h2>
    <?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>" . $_SESSION['error'] . "</p>"; unset($_SESSION['error']); } ?>
    <form action="register.php" method="POST" enctype="multipart/form-data">
    <label for="name">Full Name:</label>
    <input type="text" name="name" required>

    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="dob">Date of Birth:</label>
    <input type="date" name="dob" required> <!-- Updated Age to DOB -->

    <label for="gender">Gender:</label>
    <select name="gender" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>

    <label for="state">State:</label>
    <input type="text" name="state" required>

    <label for="city">City:</label>
    <input type="text" name="city" required>

    <label for="bank_branch">Bank Branch:</label>
    <input type="text" name="bank_branch" required>

    <label for="account_type">Account Type:</label>
    <select name="account_type" required>
        <option value="Savings">Savings</option>
        <option value="Current">Current</option>
    </select>

    <label for="transaction_location">Transaction Location:</label>
    <input type="text" name="transaction_location" required>

    <label for="contact">Contact Number:</label>
    <input type="text" name="contact" required>

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="security_question">Security Question:</label>
    <select name="security_question" required>
        <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
        <option value="What was the name of your first pet?">What was the name of your first pet?</option>
        <option value="What is your favorite book?">What is your favorite book?</option>
    </select>

    <label for="security_answer">Security Answer:</label> <!-- New field -->
    <input type="text" name="security_answer" required>

    <label for="address">Address:</label> <!-- New field -->
    <input type="text" name="address" required>

    <label for="profile_picture">Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/*">

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <button type="submit">Register</button>
</form>

    <p>Already have an account? <a href="login.php">Login</a></p>
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
<!-- Added fields -->