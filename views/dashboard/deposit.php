<?php
session_start();
require_once "../../config/database.php";
require_once "../../models/Transaction.php";
require_once "../../models/User.php";

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$transaction = new Transaction($db);
$user = new User($db);

$message = "";
$account_number = $_SESSION['account_number'];

// Get user data and balance before/after
$user_data = $user->getUserById($account_number);
$current_balance = $user_data ? $user_data['balance'] : 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST['amount'];

    if ($transaction->deposit($account_number, $amount)) {
        $message = "<p class='success'>Deposit of <strong>$$amount</strong> was successful!</p>";
        $user_data = $user->getUserById($account_number); // Refresh balance
        $current_balance = $user_data['balance'];
    } else {
        $message = "<p class='error'>Deposit failed. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Deposit Funds</title>
  <link rel="stylesheet" href="../../assets/css/user_depo.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>SmartBank</h2>
      <ul>
        <li><a href="index.php">🏠 Dashboard</a></li>
        <li><a href="deposit.php">➕ Deposit</a></li>
        <li><a href="transfer.php">💸 Transfer</a></li>
        <li><a href="../user/profile.php">👤 Profile</a></li>
        <li><a href="../../views/auth/logout.php">🚪 Logout</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <h2>Deposit Funds</h2>

      <p><strong>Current Balance:</strong> $<?= number_format($current_balance, 2) ?></p>
      <?= $message ?>

      <form method="POST" class="form-card">
        <label for="amount">Amount ($):</label>
        <input type="number" name="amount" min="1" required placeholder="Enter deposit amount">

        <button type="submit">Deposit</button>
      </form>
    </main>
  </div>
</body>
</html>
