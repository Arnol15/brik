<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['id'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

    // Verify category exists
    $check_query = "SELECT * FROM categories WHERE id = $id";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) === 1) {
        $delete_query = "DELETE FROM categories WHERE id = $id LIMIT 1";
        $delete_result = mysqli_query($connection, $delete_query);

        if ($delete_result) {
            $_SESSION['delete-category-success'] = "Category deleted successfully.";
        } else {
            $_SESSION['delete-category'] = "Failed to delete category.";
        }
    } else {
        $_SESSION['delete-category'] = "Category not found.";
    }
}

// After deletion, re-render updated category list dynamically
include __DIR__ . '/../forms/manage-category.php';
