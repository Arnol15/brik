<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

// Check if form submitted
if (isset($_POST['title'], $_POST['description'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Validate input
    if ($title === '' || $description === '') {
        $message = "All fields are required.";
        $_SESSION['add-category'] = $message;
    } else {
        // Insert into database
        $stmt = mysqli_prepare($connection, "INSERT INTO categories (title, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $title, $description);
        $insertSuccess = mysqli_stmt_execute($stmt);

        if ($insertSuccess) {
            $message = "Category added successfully.";
            $_SESSION['add-category-success'] = $message;
        } else {
            $message = "Database error: " . mysqli_error($connection);
            $_SESSION['add-category'] = $message;
        }

        mysqli_stmt_close($stmt);
    }

    // If request is from AJAX, return message only (no redirect)
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo $message;
        exit;
    }

    // Otherwise, redirect normally
    header('Location: ../index.php?page=Add+Category');
    exit;
}

// No POST data
http_response_code(400);
echo "Invalid request.";
