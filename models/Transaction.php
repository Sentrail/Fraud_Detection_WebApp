<?php
class Transaction {
    private $conn;
    private $table = "transactions";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Create Transaction (Deposit, Transfer)
    public function createTransaction($userId, $amount, $type, $status = 'pending', $senderEmail = NULL, $receiverEmail = NULL) {
        $stmt = $this->conn->prepare("INSERT INTO transactions (user_id, amount, type, status, sender_email, receiver_email) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param("idssss", $userId, $amount, $type, $status, $senderEmail, $receiverEmail);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // ✅ Get User Transactions
    public function getUserTransactions($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM transactions WHERE user_id = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ✅ Deposit Money
    public function deposit($user_id, $amount) {
        $stmt = $this->conn->prepare("UPDATE users SET balance = balance + ? WHERE account_number = ?");
        if (!$stmt) {
            die("Error preparing deposit statement: " . $this->conn->error);
        }
        $stmt->bind_param("di", $amount, $user_id);
        if ($stmt->execute()) {
            // Log transaction
            $log = $this->conn->prepare("INSERT INTO transactions (user_id, amount, transaction_type) VALUES (?, ?, 'deposit')");
            $log->bind_param("id", $user_id, $amount);
            return $log->execute();
        }
        return false;
    }

    // ✅ Transfer Money
    public function transferFunds($sender_account, $recipient_account, $amount) {
        // Check if sender has enough balance
        $stmt = $this->conn->prepare("SELECT balance FROM users WHERE account_number = ?");
        $stmt->bind_param("s", $sender_account);
        $stmt->execute();
        $stmt->bind_result($sender_balance);
        $stmt->fetch();
        $stmt->close();

        if ($sender_balance < $amount) {
            return false; // Insufficient balance
        }

        // Deduct amount from sender
        $stmt = $this->conn->prepare("UPDATE users SET balance = balance - ? WHERE account_number = ?");
        $stmt->bind_param("ds", $amount, $sender_account);
        $stmt->execute();
        $stmt->close();

        // Add amount to recipient
        $stmt = $this->conn->prepare("UPDATE users SET balance = balance + ? WHERE account_number = ?");
        $stmt->bind_param("ds", $amount, $recipient_account);
        $stmt->execute();
        $stmt->close();

        // Log transaction
        $stmt = $this->conn->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, transaction_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssd", $sender_account, $recipient_account, $amount);
        $stmt->execute();
        $stmt->close();

        return true;
    }


    // ✅ Fraud Detection
    public function checkForFraud($user_id, $amount, $recipient_id, $ip_address) {
        $fraudulent = false;
        $reason = "";

        // Rule 1: Large Amount Transfers
        if ($amount > 5000) {
            $fraudulent = true;
            $reason = "Transaction amount exceeds $5000.";
        }

        // Rule 2: Multiple Transactions in 1 Minute
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = ? AND created_at > NOW() - INTERVAL 1 MINUTE");
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            die("Error executing fraud check: " . $stmt->error);
        }
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 3) {
            $fraudulent = true;
            $reason = "More than 3 transactions in 1 minute.";
        }

        // Rule 3: Check Blacklisted Accounts
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM blacklisted_accounts WHERE user_id = ?");
        $stmt->bind_param("i", $recipient_id);
        if (!$stmt->execute()) {
            die("Error executing blacklist check: " . $stmt->error);
        }
        $stmt->bind_result($is_blacklisted);
        $stmt->fetch();
        $stmt->close();

        if ($is_blacklisted > 0) {
            $fraudulent = true;
            $reason = "Recipient is in the blacklist.";
        }

        // Rule 4: Unrecognized IP Address
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user_ips WHERE user_id = ? AND ip_address = ?");
        $stmt->bind_param("is", $user_id, $ip_address);
        if (!$stmt->execute()) {
            die("Error executing IP check: " . $stmt->error);
        }
        $stmt->bind_result($known_ip);
        $stmt->fetch();
        $stmt->close();

        if ($known_ip == 0) {
            $fraudulent = true;
            $reason = "Transaction from an unknown device or location.";
        }

        // If fraudulent, log the transaction
        if ($fraudulent) {
            $stmt = $this->conn->prepare("INSERT INTO fraud_alerts (user_id, amount, reason, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("ids", $user_id, $amount, $reason);
            $stmt->execute();
        }

        return $fraudulent;
    }
}
?>
