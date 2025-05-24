<?php
session_start();
require_once "../../config/database.php";
require_once "../../models/Transaction.php";

// Database connection
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Check login
if (!isset($_SESSION['account_number']) || empty($_SESSION['account_number'])) {
    die("Error: User is not logged in!");
}

$transaction = new Transaction($db);

// Handle AJAX check for recipient account name
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['check_account'])) {
    $recipient_account = trim($_POST['check_account']);

    $stmt = $db->prepare("SELECT name FROM users WHERE account_number = ?");
    $stmt->bind_param("s", $recipient_account);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($recipient_name);
        $stmt->fetch();
        echo json_encode(["status" => "success", "name" => $recipient_name]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid account number."]);
    }
    $stmt->close();
    exit();
}

// Handle form submission
$transfer_message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['check_account'])) {
    $recipient_account = trim($_POST['recipient_account'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $sender_id = $_SESSION['account_number'];

    if (empty($recipient_account) || empty($amount)) {
        $transfer_message = "Error: Missing recipient account number or amount!";
    } else {
        $result = $transaction->transferFunds($sender_id, $recipient_account, $amount);
        if ($result) {
            $transfer_message = "Success: Transfer completed.";
        } else {
            $transfer_message = "Error: Transfer failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Transfer Funds</title>
    <link rel="stylesheet" href="../../assets/css/user_trans.css" />
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

    <main class="main-content">
        <h1>Transfer Funds</h1>

        <?php if (!empty($transfer_message)): ?>
            <p class="<?= strpos($transfer_message, 'Success') === 0 ? 'success-msg' : 'error-msg' ?>">
                <?= htmlspecialchars($transfer_message) ?>
            </p>
        <?php endif; ?>

        <form method="post" id="transferForm">
            <label for="recipient_account">Recipient Account Number</label>
            <input type="text" id="recipient_account" name="recipient_account" placeholder="Enter account number" required />
            <p id="account_holder_name"></p>

            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" required placeholder="Enter transfer amount" min="100" step="any" />

            <button type="submit">Transfer</button>
        </form>
    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const recipientInput = document.getElementById("recipient_account");
    const nameDisplay = document.getElementById("account_holder_name");

    recipientInput.addEventListener("input", function() {
        let accountNumber = this.value.trim();
        if (accountNumber.length > 0) {
            fetch("transfer.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "check_account=" + encodeURIComponent(accountNumber)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    nameDisplay.textContent = "Account Holder: " + data.name;
                    nameDisplay.style.color = "green";
                } else {
                    nameDisplay.textContent = "Invalid account number.";
                    nameDisplay.style.color = "red";
                }
            });
        } else {
            nameDisplay.textContent = "";
        }
    });
});
</script>
</body>
</html>
