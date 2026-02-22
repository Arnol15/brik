<?php
session_start();
require __DIR__ . '/../../config/database.php'; // Secure absolute path

if (isset($_POST['submit'])) {

    // 1. Check login
    if (empty($_SESSION['user-id'])) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'You must be logged in to add a post.'];
        header('Location: ../index.php?page=Add Post');
        exit;
    }

    $author_id = (int)$_SESSION['user-id'];

    // 2. Sanitize inputs
    $title = trim(filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $body = trim($_POST['body']);
    $body = strip_tags($body, '<p><br><b><strong><i><em><u><a><img><ul><ol><li><table><tr><td><th><blockquote><code><pre><h1><h2><h3><h4><h5><h6><span><div><hr>');
    $category_id = (int)($_POST['category'] ?? 0);
    $is_featured = (int)($_POST['is_featured'] ?? 0);
    $thumbnail = $_FILES['thumbnail'] ?? null;

    // 3. Validate form
    if (!$title || !$body || !$category_id || !$thumbnail['name']) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'All fields (title, category, content, thumbnail) are required.'];
        $_SESSION['add-post-data'] = $_POST;
        header('Location: ../index.php?page=Add Post');
        exit;
    }

    // 4. Prepare upload directory (relative to project root)
    $uploadDir = realpath(__DIR__ . '/../../images/posts/');
    if (!$uploadDir) {
        mkdir(__DIR__ . '/../../images/posts/', 0777, true);
        $uploadDir = realpath(__DIR__ . '/../../images/posts/');
    }

    // 5. Secure file naming
    $extension = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($extension, $allowed_ext)) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP, SVG allowed.'];
        header('Location: ../index.php?page=Add Post');
        exit;
    }

    $thumbnail_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($thumbnail['name']));
    $thumbnail_path = $uploadDir . DIRECTORY_SEPARATOR . $thumbnail_name;

    // 6. Validate file size
    if ($thumbnail['size'] > 10_000_000) { // 10MB
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Image file too large (max 10MB).'];
        header('Location: ../index.php?page=Add Post');
        exit;
    }

    // 7. Validate MIME
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $thumbnail['tmp_name']);
    finfo_close($file_info);
    $allowed_mimes = [
        'image/png', 'image/jpeg', 'image/jpg', 'image/gif',
        'image/webp', 'image/svg+xml'
    ];
    if (!in_array($mime_type, $allowed_mimes)) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Invalid image file type.'];
        header('Location: ../index.php?page=Add Post');
        exit;
    }

    // 8. Move uploaded file
    if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnail_path)) {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to upload image.'];
        header('Location: ../index.php?page=Add Post');
        exit;
    }

    // 9. Build DB relative path (this ensures render works in HTML)
    $thumbnail_db_path = '/images/posts/' . $thumbnail_name;

    // 10. Set featured post
    if ($is_featured === 1) {
        mysqli_query($connection, "UPDATE posts SET is_featured = 0");
    }

    // 11. Insert post
    $stmt = mysqli_prepare($connection, "
        INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, 'sssiii',
        $title,
        $body,
        $thumbnail_db_path,
        $category_id,
        $author_id,
        $is_featured
    );

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => "Post '$title' added successfully!"];
        header('Location: ../index.php?page=Manage Posts');
        exit;
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Database error: ' . mysqli_error($connection)];
        header('Location: ../index.php?page=Add Post');
        exit;
    }
} else {
    header('Location: ../index.php?page=Add Post');
    exit;
}
?>
