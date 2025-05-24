<?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "../../config/database.php";
    require_once "../../models/User.php";

    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $userModel = new User($db);

    // Capture Form Data
    $name = $_POST['name'];
    $dob = date('Y-m-d', strtotime($_POST['dob'])); // Convert input to correct MySQL format New Date of Birth Field
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $bank_branch = $_POST['bank_branch'];
    $account_type = $_POST['account_type'];
    $transaction_location = $_POST['transaction_location'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Process Image Upload
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
    $profile_picture = basename($_FILES["profile_picture"]["name"]);

    // Register User
    $registrationSuccess = $userModel->register(
        $name, $dob, $gender, $state, $city, $bank_branch, $account_type, 
        $transaction_location, $contact, $email, $security_question, 
        $security_answer, $address, $profile_picture, $password
    );

    if ($registrationSuccess) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Registration failed. Please try again.";
    }
}
