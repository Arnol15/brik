<?php
session_start();
require '../config/database.php';

if (isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = $_POST['createpassword'];
    $confirmpassword = $_POST['confirmpassword'];
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);
    $avatar = $_FILES['avatar'];

    // Validate inputs
    if (!$firstname || !$lastname || !$username || !$email) {
        $_SESSION['add-user'] = "Please fill in all fields correctly.";
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['add-user'] = "Password should be at least 8 characters long.";
    } elseif ($createpassword !== $confirmpassword) {
        $_SESSION['add-user'] = "Passwords do not match.";
    } elseif (!$avatar['name']) {
        $_SESSION['add-user'] = "Please upload an avatar image.";
    } else {
        // Check if username/email exists
        $check = $connection->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['add-user'] = "Username or email already exists.";
        } else {
            // Ensure upload folder exists
            $upload_dir = '../images/authors/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Handle avatar upload
            $time = time();
            $safe_name = preg_replace("/[^a-zA-Z0-9-_\.]/", "", basename($avatar['name']));
            $avatar_name = $time . "_" . $safe_name;
            $avatar_tmp_name = $avatar['tmp_name'];
            $avatar_destination_path = $upload_dir . $avatar_name;

            $allowed_ext = ['png', 'jpg', 'jpeg'];
            $ext = strtolower(pathinfo($avatar_name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                $_SESSION['add-user'] = "Invalid image format. Use png, jpg, or jpeg.";
            } elseif ($avatar['size'] > 10000000) {
                $_SESSION['add-user'] = "Image file too large (max 10MB).";
            } else {
                move_uploaded_file($avatar_tmp_name, $avatar_destination_path);

                // Hash password
                $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

                // Insert user
                $stmt = $connection->prepare(
                    "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param("ssssssi", $firstname, $lastname, $username, $email, $hashed_password, $avatar_name, $is_admin);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['add-user-success'] = "$firstname $lastname added successfully!";
                    header('location: ' . ROOT_URL . 'admin/forms/add-user.php');
                    exit;
                } else {
                    $_SESSION['add-user'] = "Failed to add user. Try again.";
                }
            }
        }
    }

    // On error — redirect back with form data
    if (isset($_SESSION['add-user'])) {
        $_SESSION['add-user-data'] = $_POST;
        header('location: ' . ROOT_URL . 'admin/forms/add-user.php');
        exit;
    }
} else {
    header('location: ' . ROOT_URL . 'admin/forms/add-user.php');
    exit;
}
