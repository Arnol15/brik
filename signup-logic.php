<?php
session_start();
require 'config/database.php'; // DB connection
require 'config/constants.php'; // ROOT_URL definition

if (isset($_POST['submit'])) {
    // 1️⃣ Sanitize input
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = $_POST['createpassword'];
    $confirmpassword = $_POST['confirmpassword'];
    $avatar = $_FILES['avatar'];

    // 2️⃣ Basic validation
    if (!$firstname || !$lastname || !$username || !$email) {
        $_SESSION['signup'] = "All fields are required.";
    } elseif (strlen($createpassword) < 8) {
        $_SESSION['signup'] = "Password must be at least 8 characters.";
    } elseif ($createpassword !== $confirmpassword) {
        $_SESSION['signup'] = "Passwords do not match.";
    }

    // If validation failed
    if (isset($_SESSION['signup'])) {
        $_SESSION['signup-data'] = $_POST;
        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    // 3️⃣ Check if username or email already exists
    $check_user_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $connection->prepare($check_user_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['signup'] = "Username or email already exists.";
        $_SESSION['signup-data'] = $_POST;
        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

    // 4️⃣ Handle avatar upload
    $avatar_name = "default.png"; // fallback
    if (!empty($avatar['name'])) {
        $time = time();
        $avatar_name = $time . "_" . basename($avatar['name']);
        $avatar_tmp = $avatar['tmp_name'];
        $avatar_size = $avatar['size'];
        $avatar_ext = strtolower(pathinfo($avatar_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($avatar_ext, $allowed)) {
            $_SESSION['signup'] = "Invalid file type. Only JPG, JPEG, PNG allowed.";
        } elseif ($avatar_size > 2 * 1024 * 1024) { // 2MB
            $_SESSION['signup'] = "Image too large. Must be less than 2MB.";
        } else {
            $upload_dir = __DIR__ . '/images/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($avatar_tmp, $upload_dir . $avatar_name);
        }

        // If upload failed
        if (isset($_SESSION['signup'])) {
            $_SESSION['signup-data'] = $_POST;
            header("Location: " . ROOT_URL . "signup.php");
            exit;
        }
    }

    // 5️⃣ Hash the password
    $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

    // 6️⃣ Insert new user with admin flag (default 0) and timestamp
    $is_admin = 0; // default non-admin
    $created_at = date('Y-m-d H:i:s');

    $insert_query = "INSERT INTO users 
        (firstname, lastname, username, email, password, avatar, is_admin, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($insert_query);
    $stmt->bind_param("ssssssis", $firstname, $lastname, $username, $email, $hashed_password, $avatar_name, $is_admin, $created_at);

    if ($stmt->execute()) {
        $_SESSION['signup-success'] = "Registration successful! Please log in.";
        header("Location: " . ROOT_URL . "signin.php");
        exit;
    } else {
        $_SESSION['signup'] = "Database error. Please try again.";
        $_SESSION['signup-data'] = $_POST;
        header("Location: " . ROOT_URL . "signup.php");
        exit;
    }

} else {
    header("Location: " . ROOT_URL . "signup.php");
    exit;
}
?>


