<?php
session_start();
require __DIR__ . '/config/database.php'; // your DB connection
require __DIR__ . '/config/constants.php'; // ROOT_URL definition

// ✅ Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // 1️⃣ Sanitize input
    $username_email = filter_var(trim($_POST['username_email'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = trim($_POST['password'] ?? '');

    // 2️⃣ Validate input
    if (empty($username_email)) {
        $_SESSION['signin'] = "Please enter your Username or Email.";
    } elseif (empty($password)) {
        $_SESSION['signin'] = "Please enter your Password.";
    }

    // 3️⃣ If validation failed → redirect back
    if (isset($_SESSION['signin'])) {
        $_SESSION['signin-data'] = $_POST;
        header("Location: " . ROOT_URL . "signin.php");
        exit;
    }

    // 4️⃣ Query the database securely
    $query = "SELECT id, username, email, password, avatar, is_admin FROM users WHERE username = ? OR email = ? LIMIT 1";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // 5️⃣ Handle user existence
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 6️⃣ Verify password
        if (password_verify($password, $user['password'])) {
            // ✅ Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;

            // Regenerate session ID for security
            session_regenerate_id(true);

            // 7️⃣ Redirect based on role
            if ($user['is_admin'] == 1) {
                header("Location: " . ROOT_URL . "admin/index.php");
            } else {
                header("Location: " . ROOT_URL . "index.php");
            }
            exit;
        } else {
            $_SESSION['signin'] = "Incorrect password. Please try again.";
        }
    } else {
        $_SESSION['signin'] = "No account found with that Username or Email.";
    }

    // 8️⃣ Redirect back with error message
    $_SESSION['signin-data'] = $_POST;
    header("Location: " . ROOT_URL . "signin.php");
    exit;

} else {
    // 🚫 Prevent direct URL access
    header("Location: " . ROOT_URL . "signin.php");
    exit;
}
?>
