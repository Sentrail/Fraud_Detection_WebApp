<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register(
        $username, $name, $dob, $gender, $state, $city, $bank_branch, $account_type, 
        $transaction_location, $contact, $email, $security_question, 
        $security_answer, $address, $profile_picture, $password
    ) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);        
        $account_number = mt_rand(1000000000, 9999999999); // 10-digit random number

        // Check if the email already exists
        $checkEmailQuery = "SELECT email FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return "Error: Email already registered! Please use another email.";
        }
        $stmt->close();

        // Prepare SQL query
        $query = "INSERT INTO users (username, account_number, name, dob, gender, state, city, bank_branch, account_type, transaction_location, contact, email, security_question, security_answer, address, profile_picture, password) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return "Error preparing query: " . $this->conn->error;
        }
        $stmt->bind_param("sssssssssssssssss", $username, $account_number, $name, $dob, $gender, $state, $city, $bank_branch, $account_type, $transaction_location, $contact, $email, $security_question, $security_answer, $address, $profile_picture, $hashed_password);
        if ($stmt->execute()) {
            return true;
        } else {
            return "Registration failed: " . $stmt->error;
        }
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    public function getUserById($account_number) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE account_number = ?");
        $stmt->bind_param("s", $account_number); // Account number is a string (10-digit)
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_assoc();
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT account_number, password FROM users WHERE username = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($account_number, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                return $account_number; // Return account_number on success
            }
        }
        $stmt->close();
        return false; // Login failed
    }

    // Removed redundant loginUser method
    // Keep loginAdmin if needed for admin functionality
    public function loginAdmin($username, $password) {
        $stmt = $this->conn->prepare("SELECT password, role FROM admin WHERE username = ?");
        if (!$stmt) {
            die("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password, $role);
        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password) && $role === 'admin') {
                $stmt->close();
                return true;
            }
        }
        $stmt->close();
        return false;
    }
}
?>