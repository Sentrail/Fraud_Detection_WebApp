<?php
session_start();
$page_title = "Admin Dashboard";
require_once "../../config/database.php";
// require_once "../../templates/header.php";
// require_once "../../templates/footer.php";


// Redirect if not admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin_login.php');
    exit();
}

// Connect to DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get dashboard data
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_transactions = $conn->query("SELECT COUNT(*) FROM transactions")->fetch_row()[0];
$flagged_transactions = $conn->query("SELECT COUNT(*) FROM transactions WHERE status = 'flagged'")->fetch_row()[0];
$users_result = $conn->query("SELECT account_number, name, email FROM users LIMIT 10");
$tx_result = $conn->query("SELECT id, user_id, amount, status FROM transactions ORDER BY id DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator</title>
      <link rel="stylesheet" href="../../assets/css/admin_dash.css">
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <div class="logo"><h2>FraudGuard</h2></div>
        <div class="admin-profile">
            <p>Welcome, Admin</p>
            <p class="admin-role">System Administrator</p>
        </div>
        <nav class="nav-menu">
            <ul>
                <li class="active"><a href="#dashboard">Dashboard</a></li>
                <li><a href="#users">Users</a></li>
                <li><a href="#transactions">Transactions</a></li>
                <li><a href="#analytics">Analytics</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <header class="dashboard-header">
            <div class="search-container">
                <input type="text" placeholder="Search..." id="search-input">
                <button id="search-btn">Search</button>
            </div>
            <div class="header-right">
                <div class="notification"><span class="notification-count">3</span></div>
                <div class="date-time" id="current-date-time"></div>
            </div>
        </header>

        <section id="dashboard" class="content-section">
            <h2>Dashboard Overview</h2>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?= $total_users ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Transactions</h3>
                    <p class="stat-number"><?= $total_transactions ?></p>
                </div>
                <div class="stat-card">
                    <h3>Flagged Transactions</h3>
                    <p class="stat-number"><?= $flagged_transactions ?></p>
                </div>
                <div class="stat-card">
                    <h3>System Health</h3>
                    <p class="stat-number"><?= $system_health ?></p>
                </div>
            </div>
        </section>

        <section id="users" class="content-section">
            <h2>User Management</h2>
            <p>List and manage all user accounts</p>
            <table>
            <tr><th>ID</th><th>Name</th><th>Email</th></tr>
            <?php while ($row = $users_result->fetch_assoc()): ?>
            <tr><td><?= $row['account_number'] ?></td><td><?= $row['name'] ?></td><td><?= $row['email'] ?></td></tr>
            <?php endwhile; ?>
            </table>
        </section>

        <section id="transactions" class="content-section">
            <h2>Transaction Monitoring</h2>
            <p>Display and review all transactions</p>
            <table>
                <tr><th>ID</th><th>User</th><th>Amount</th><th>Status</th></tr>
                <?php while ($row = $tx_result->fetch_assoc()): ?>
                <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['user_id'] ?></td>
                <td>$<?= $row['amount'] ?></td>
                <td class="<?= $row['status'] === 'flagged' ? 'flagged' : '' ?>"><?= $row['status'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>

        <section id="analytics" class="content-section">
            <h2>Fraud Analytics</h2>
            <p>Coming soon: performance metrics and detection charts</p>
        </section>
    </div>
</div>

<script>
    document.getElementById("current-date-time").innerText = new Date().toLocaleString();
</script>
</body>
</html>

