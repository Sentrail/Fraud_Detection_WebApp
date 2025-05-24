<!-- <?php
session_start();
require_once '../../config/database.php';
require_once '../../models/User.php';

if (!isset($_SESSION['account_number'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_picture'])) {
    $userId = $_SESSION['account_number'];
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $user = new User($db);

    $target_dir = "../../uploads/";
    $filename = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        if ($user->updateProfilePicture($userId, $filename)) {
            $_SESSION['profile_picture'] = $filename;
            header("Location: index.php");
            exit();
        }
    }
    echo "Error uploading file.";
}
?> -->
