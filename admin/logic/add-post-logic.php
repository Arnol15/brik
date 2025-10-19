<?php 
require 'config/database.php';
session_start();

if (isset($_POST['submit'])) {
    // Ensure user is logged in
    if (!isset($_SESSION['user-id'])) {
        $_SESSION['add-post'] = "You must be logged in to add a post.";
        header('Location: ../add-post.php');
        exit;
    }

    $author_id = $_SESSION['user-id'];
    $title = trim(filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $body = trim(filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // Basic validation
    if (!$title) {
        $_SESSION['add-post'] = "Enter Post Title.";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Select Post Category.";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Enter Post Content.";
    } elseif (!$thumbnail['name']) {
        $_SESSION['add-post'] = "Upload a thumbnail image.";
    } else {
        // Prepare upload directory
        $uploadDir = '../../images/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $time = time();
        $thumbnail_name = $time . '_' . basename($thumbnail['name']);
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination = $uploadDir . $thumbnail_name;

        // ✅ Allow all image MIME types
        $allowed_mime_types = [
            'image/png', 'image/jpeg', 'image/jpg', 'image/gif',
            'image/webp', 'image/bmp', 'image/tiff', 'image/svg+xml'
        ];

        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $thumbnail_tmp_name);
        finfo_close($file_info);

        if (in_array($mime_type, $allowed_mime_types)) {
            if ($thumbnail['size'] < 10_000_000) { // 10MB max
                if (move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination)) {
                    // If featured, reset other featured posts
                    if ($is_featured == 1) {
                        mysqli_query($connection, "UPDATE posts SET is_featured = 0");
                    }

                    // Insert post safely
                    $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured)
                              VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($connection, $query);
                    mysqli_stmt_bind_param($stmt, 'sssiii', 
                        $title, $body, $thumbnail_name, $category_id, $author_id, $is_featured
                    );
                    $result = mysqli_stmt_execute($stmt);

                    if ($result) {
                        $_SESSION['add-post-success'] = "New post added successfully.";
                        header('Location: ../forms/manage-posts.php');
                        exit;
                    } else {
                        $_SESSION['add-post'] = "Database error: " . mysqli_error($connection);
                    }
                } else {
                    $_SESSION['add-post'] = "Failed to move uploaded file.";
                }
            } else {
                $_SESSION['add-post'] = "Image size too large (max 10MB).";
            }
        } else {
            $_SESSION['add-post'] = "Invalid image type. Please upload a valid image file.";
        }
    }

    // Redirect back if something failed
    if (isset($_SESSION['add-post'])) {
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ../add-post.php');
        exit;
    }
} else {
    header('Location: ../add-post.php');
    exit;
}
?>
