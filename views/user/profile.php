<?php
session_start();

// Redirect if user is not logged in
if (empty($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

require_once "../../config/database.php";
require_once "../../models/Transaction.php";

// Initialize database connection
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Secure query to fetch user
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

// Remove unnecessary session variable assignments
// $_SESSION['account_number'] and $_SESSION['username'] are already set in login.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../assets/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="../../Uploads/<?= htmlspecialchars($user['profile_picture']) ?>" class="profile-pic" alt="Profile Picture">
            <h2><?= htmlspecialchars($user['fullname']) ?></h2>
            <p>Username: <?= htmlspecialchars($user['username']) ?></p>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Phone: <?= htmlspecialchars($user['phone']) ?></p>
            <p>DOB: <?= htmlspecialchars($user['dob']) ?></p>
            <p>Gender: <?= htmlspecialchars($user['gender']) ?></p>
            <p>Account Type: <?= htmlspecialchars($user['account_type']) ?></p>
            <div class="profile-actions">
                <a href="edit_profile.php">Edit Profile</a>
                <a href="change_password.php">Change Password</a>
                <a href="deactivate_account.php" class="danger">Deactivate Account</a>
            </div>
        </div>

        <div class="notification-settings">
            <h3>Notification Preferences</h3>
            <form action="update_notifications.php" method="post">
                <label><input type="checkbox" name="notify_email" <?= $user['notify_email'] ? 'checked' : '' ?>> Email</label><br>
                <label><input type="checkbox" name="notify_sms" <?= $user['notify_sms'] ? 'checked' : '' ?>> SMS</label><br>
                <button type="submit">Update Preferences</button>
            </form>
        </div>

        <div class="fraud-alerts">
            <h3>Fraud Alerts</h3>
            <p><?= htmlspecialchars($user['fraud_count']) ?> suspicious transaction(s) detected.</p>
            <a href="fraud_alerts.php">View Details</a>
        </div>

        <div class="transactions">
            <h3>Transaction History</h3>
            <a href="download_report.php">Download Report</a>
            <table>
                <thead>
                    <tr><th>Date</th><th>Amount</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php
                    // Use prepared statement for transaction query
                    $stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $txns = $stmt->get_result();
                    while ($txn = $txns->fetch_assoc()) {
                        echo "<tr><td>" . htmlspecialchars($txn['date']) . "</td><td>" . htmlspecialchars($txn['amount']) . "</td><td>" . htmlspecialchars($txn['status']) . "</td></tr>";
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>

        <div class="logout">
            <a href="../../logout.php">Logout</a>
        </div>
    </div>
</body>
</html>