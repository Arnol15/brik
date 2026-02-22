<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $thumbnail = $_FILES['thumbnail'];

    // --- Validate basic form data ---
    if (!$title || !$body || !$category_id) {
        $_SESSION['edit-post'] = "Please fill all required fields.";
        header('location: ' . ROOT_URL . "admin/edit-post.php?id=$id");
        die();
    }

    // --- Handle new thumbnail if provided ---
    if ($thumbnail['name']) {
        $allowed_exts = ['png', 'jpg', 'jpeg'];
        $extension = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $allowed_exts)) {
            if ($thumbnail['size'] < 10_000_000) { // < 10MB
                // delete old thumbnail
                $old_path = '../images/posts/' . $previous_thumbnail_name;
                if (file_exists($old_path)) unlink($old_path);

                // upload new thumbnail
                $new_name = time() . '.' . $extension;
                $destination = '../images/posts/' . $new_name;
                move_uploaded_file($thumbnail['tmp_name'], $destination);
                $thumbnail_to_save = $new_name;
            } else {
                $_SESSION['edit-post'] = "Thumbnail too large. Must be < 10MB.";
                header('location: ' . ROOT_URL . "admin/edit-post.php?id=$id");
                die();
            }
        } else {
            $_SESSION['edit-post'] = "Invalid thumbnail format. Use JPG, PNG, or JPEG.";
            header('location: ' . ROOT_URL . "admin/edit-post.php?id=$id");
            die();
        }
    } else {
        // keep previous thumbnail
        $thumbnail_to_save = $previous_thumbnail_name;
    }

    // --- If featured, remove featured flag from others ---
    if ($is_featured === 1) {
        $reset_query = "UPDATE posts SET is_featured = 0";
        mysqli_query($connection, $reset_query);
    }

    // --- Update post ---
    $query = "UPDATE posts 
              SET title = '$title', body = '$body', category_id = $category_id,
                  is_featured = $is_featured, thumbnail = '$thumbnail_to_save'
              WHERE id = $id LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (!mysqli_errno($connection)) {
        $_SESSION['edit-post-success'] = "Post updated successfully.";
    } else {
        $_SESSION['edit-post'] = "Database error: failed to update post.";
    }

    header('location: ' . ROOT_URL . 'admin/forms/');
    die();
} else {
    header('location: ' . ROOT_URL . 'admin/forms/');
    die();
}
