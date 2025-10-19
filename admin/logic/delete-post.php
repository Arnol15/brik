<?php
require '../../config/database.php';
session_start();

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch post details first
    $query = "SELECT thumbnail, body FROM posts WHERE id = $id LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $post = mysqli_fetch_assoc($result);

        // 1️ Delete Thumbnail
        $thumbnailPath = '../../images/' . $post['thumbnail'];
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }

        // 2️ Delete TinyMCE inline images from body
        $body = $post['body'];

        // Extract image src attributes that point to /images/posts/
        preg_match_all('/src=["\']([^"\']*images\/posts\/[^"\']+)["\']/', $body, $matches);
        $imagePaths = $matches[1] ?? [];

        foreach ($imagePaths as $imgUrl) {
            // Remove ROOT_URL if present
            $imgFile = str_replace(ROOT_URL, '../../', $imgUrl);

            // Ensure file path is safe and inside the project directory
            if (file_exists($imgFile) && str_starts_with(realpath($imgFile), realpath('../../images/posts'))) {
                unlink($imgFile);
            }
        }

        // 3️⃣ Finally delete the post
        $deleteQuery = "DELETE FROM posts WHERE id = $id LIMIT 1";
        mysqli_query($connection, $deleteQuery);

        if (!mysqli_errno($connection)) {
            $_SESSION['delete-post-success'] = "Post deleted successfully.";
        } else {
            $_SESSION['delete-post'] = "Database error: " . mysqli_error($connection);
        }
    } else {
        $_SESSION['delete-post'] = "Post not found.";
    }
} else {
    $_SESSION['delete-post'] = "Invalid request.";
}

header('Location: ../manage-posts.php');
exit;
?>
