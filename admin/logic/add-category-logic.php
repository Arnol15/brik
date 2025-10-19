<?php
require '../../config/database.php';
session_start();

if (isset($_POST['submit'])) {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // validate input
    if (!$title) {
        $_SESSION['add-category'] = "Category title is required.";
    } elseif (!$description) {
        $_SESSION['add-category'] = "Category description is required.";
    }

    // if any error, go back
    if (isset($_SESSION['add-category'])) {
        $_SESSION['add-category-data'] = $_POST;
        header('location: ../forms/add-category.php');
        exit;
    }

    // insert into database
    $query = "INSERT INTO categories (title, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $title, $description);
    $inserted = mysqli_stmt_execute($stmt);

    if ($inserted) {
        $_SESSION['add-category-success'] = "Category '$title' added successfully!";
        header('location: ../forms/manage-categories.php');
        exit;
    } else {
        $_SESSION['add-category'] = "Something went wrong. Please try again.";
        header('location: ../forms/add-category.php');
        exit;
    }
} else {
    header('location: ../forms/add-category.php');
    exit;
}
