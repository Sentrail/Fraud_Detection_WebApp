<?php
session_start();
if (!isset($_SESSION['account_number'])) {
    header("Location: ../auth/login.php"); // Redirect if not logged in
    exit();
}

include "../../config/database.php";
include "../../models/User.php";
include "../../models/Transaction.php";

// Connect to DB
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$userModel = new User($db);
$transactionModel = new Transaction($db);

// Get user details
$userId = $_SESSION['account_number'];
$user_data = $userModel->getUserById($userId); // Fetch user details from DB

// Check if user data is retrieved
if (!$user_data) {
    echo "Error: User not found.";
    exit();
}

// Store user details in session (to avoid multiple DB calls)
$_SESSION['email'] = $user_data['email'];
$_SESSION['balance'] = $user_data['balance'];
$account_number = $_SESSION['account_number'];

// Get user transactions
$transactions = $transactionModel->getUserTransactions($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/user_dash.css">
</head>
<body>
    <div class="wrapper">
        <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<div class="dashboard-container">
  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
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
    <h2>Welcome, <?= htmlspecialchars($user_data['name']) ?>!</h2>
    <p>Email: <?= htmlspecialchars($user_data['email']) ?></p>
    <p>Your Account Number: <strong><?= htmlspecialchars($account_number) ?></strong></p>
    <h3>Account Balance: $<?= number_format($user_data['balance'], 2) ?></h3>

    <!-- Profile Picture -->
    <!-- <section class="profile-picture-section">
      <h3>Your Profile Picture</h3>
      <img src="../../uploads/<?= htmlspecialchars($user_data['profile_picture']) ?>" alt="Profile" class="profile-img">
      <form method="POST" action="update_profile_picture.php" enctype="multipart/form-data">
        <input type="file" name="profile_picture" accept="image/*" required>
        <button type="submit">Update Profile Picture</button>
      </form>
    </section> -->

    <!-- Transactions -->
    <h3>Your Transactions</h3>
    <table>
      <thead>
        <tr>
          <th>Type</th>
          <th>Amount</th>
          <th>Sender</th>
          <th>Receiver</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($transactions)): ?>
          <?php foreach ($transactions as $transaction): ?>
            <tr>
              <td data-label="Type"><?= ucfirst($transaction['type']) ?></td>
              <td data-label="Amount">$<?= number_format($transaction['amount'], 2) ?></td>
              <td data-label="Sender"><?= htmlspecialchars($transaction['sender_email']) ?: 'N/A' ?></td>
              <td data-label="Receiver"><?= htmlspecialchars($transaction['receiver_email']) ?: 'N/A' ?></td>
              <td data-label="Date"><?= date("Y-m-d H:i", strtotime($transaction['created_at'])) ?></td>
              <td data-label="Status">
                <?php
                $status = ucfirst($transaction['status']);
                if ($transaction['status'] === 'successful') echo "<span style='color:green;'>$status</span>";
                elseif ($transaction['status'] === 'pending') echo "<span style='color:orange;'>$status</span>";
                else echo "<span style='color:red;'>$status</span>";
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">No transactions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</div>
    </div>

    <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("collapsed");
    }
  </script>
</body>

</html>
