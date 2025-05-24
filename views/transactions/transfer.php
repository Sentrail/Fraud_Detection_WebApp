<!-- <?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../../config/database.php";
include "../../models/User.php";
include "../../models/Transaction.php";

// Connect to DB
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$userModel = new User($db);
$transactionModel = new Transaction($db);

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['user_id'];
    $recipient_email = trim($_POST['recipient_email']);
    $amount = floatval($_POST['amount']);

    // Validate recipient
    $recipient = $userModel->getUserByEmail($recipient_email);
    if (!$recipient) {
        $error = "Recipient not found.";
    } elseif ($amount <= 0) {
        $error = "Invalid amount.";
    } elseif ($_SESSION['balance'] < $amount) {
        $error = "Insufficient balance.";
    } else {
        // Perform transaction
        $recipient_id = $recipient['id'];
        $status = "successful"; // You can add pending verification if needed
        $transactionModel->transferCash($sender_id, $recipient_id, $amount, $status);

        // Update session balance
        $_SESSION['balance'] -= $amount;

        $success = "Transfer successful!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <h2>Transfer Money</h2>

    <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="post">
        <label for="recipient_email">Recipient Email:</label>
        <input type="email" name="recipient_email" required>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" step="0.01" required>

        <button type="submit">Transfer</button>
    </form>

    <a href="../dashboard/index.php">Back to Dashboard</a>
</body>
</html> -->
