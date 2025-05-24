<?php
// Database configuration file
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");  // Leave this empty for XAMPP
define("DB_NAME", "fraud_detection_db");

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
