<?php
require_once __DIR__ . 'config/database.php';
session_start();

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id && $id > 0) {
        // Use prepared statement for safety
        $stmt = mysqli_prepare($connection, "DELETE FROM categories WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);

        if ($success && mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['delete-category-success'] = "✅ Category deleted successfully.";
        } else {
            $_SESSION['delete-category-error'] = "⚠️ Failed to delete category or category not found.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['delete-category-error'] = "⚠️ Invalid category ID.";
    }
} else {
    $_SESSION['delete-category-error'] = "⚠️ No category ID provided.";
}

// Redirect back to manage page
header('Location: ' . ROOT_URL . 'admin/forms/manage-categories.php');
exit;
