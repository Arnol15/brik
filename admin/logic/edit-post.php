<?php
require 'config/database.php';

// --- Fetch all categories for the dropdown ---
$category_query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $category_query);

// --- Fetch post data from DB if ID is provided ---
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM posts WHERE id = $id";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {
        $post = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['edit-post'] = "Post not found.";
        header('location: ' . ROOT_URL . 'admin/forms/');
        die();
    }
} else {
    header('location: ' . ROOT_URL . 'admin/forms/');
    die();
}
?>

<!-- Edit Post Form -->
<section class="form__section">
    <div class="container form__section-container">
        <h2 class="form-title">Edit Post</h2>

        <?php if (isset($_SESSION['edit-post'])): ?>
            <div class="alert__message error">
                <p><?= $_SESSION['edit-post']; unset($_SESSION['edit-post']); ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>admin/edit-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="previous_thumbnail_name" value="<?= $post['thumbnail'] ?>">

            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" placeholder="Title" required>

            <select name="category" required>
                <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?= $category['id'] ?>" <?= $category['id'] == $post['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <textarea rows="10" name="body" placeholder="Post Description" required><?= htmlspecialchars($post['body']) ?></textarea>

            <div class="form__control inline">
                <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= $post['is_featured'] ? 'checked' : '' ?>>
                <label for="is_featured">Featured</label>
            </div>

            <div class="form__control">
                <label for="thumbnail">Change Thumbnail (optional)</label>
                <input type="file" name="thumbnail" id="thumbnail" accept=".png, .jpg, .jpeg">
            </div>

            <button type="submit" name="submit" class="btn">Update Post</button>
        </form>
    </div>
</section>
