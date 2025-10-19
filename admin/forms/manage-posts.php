<?php


// Fetch posts with category name
$query = "SELECT posts.id, posts.title, posts.thumbnail, categories.title AS category_title 
          FROM posts 
          JOIN categories ON posts.category_id = categories.id 
          ORDER BY posts.id DESC";
$posts = mysqli_query($connection, $query);
?>

<div class="content-wrapper">
    <h2 class="mb-3">Manage Posts</h2>

    <!-- Session Messages -->
    <?php if (isset($_SESSION['add-post-success'])): ?>
        <div class="alert__message success">
            <p><?= $_SESSION['add-post-success']; unset($_SESSION['add-post-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['edit-post-success'])): ?>
        <div class="alert__message success">
            <p><?= $_SESSION['edit-post-success']; unset($_SESSION['edit-post-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['delete-post-success'])): ?>
        <div class="alert__message success">
            <p><?= $_SESSION['delete-post-success']; unset($_SESSION['delete-post-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['delete-post'])): ?>
        <div class="alert__message error">
            <p><?= $_SESSION['delete-post']; unset($_SESSION['delete-post']); ?></p>
        </div>
    <?php endif; ?>

    <!-- Posts Table -->
    <?php if (mysqli_num_rows($posts) > 0): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                        <tr>
                            <td>
                                <img src="../../images/<?= htmlspecialchars($post['thumbnail']); ?>" 
                                     alt="<?= htmlspecialchars($post['title']); ?>" width="80">
                            </td>
                            <td><?= htmlspecialchars($post['title']); ?></td>
                            <td><?= htmlspecialchars($post['category_title']); ?></td>
                            <td>
                                <a href="edit-post.php?id=<?= $post['id']; ?>" class="btn sm">Edit</a>
                                <a href="../logic/delete-post.php?id=<?= $post['id']; ?>" 
                                   class="btn sm danger"
                                   onclick="return confirmDelete(event, '<?= addslashes($post['title']); ?>');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert__message error">No posts found.</div>
    <?php endif; ?>
</div>

<!-- JS Confirmation -->
<script>
function confirmDelete(event, title) {
    event.preventDefault();
    const link = event.currentTarget;
    if (confirm(`Are you sure you want to permanently delete the post "${title}"?\nThis will remove its thumbnail and data.`)) {
        window.location.href = link.href;
    }
}
</script>

<!-- Inline minimal responsive CSS -->
<style>
.table-container {
    width: 100%;
    overflow-x: auto;
}
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}
.table th, .table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.table img {
    border-radius: 6px;
}
.btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
}
.btn.sm {
    padding: 5px 10px;
}
.btn.danger {
    background: #e74c3c;
    color: #fff;
}
.btn.danger:hover {
    background: #c0392b;
}
.alert__message {
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 1rem;
}
.alert__message.success { background: #e8f8f5; color: #1e8449; }
.alert__message.error { background: #fdecea; color: #c0392b; }

@media(max-width: 768px) {
    .table th, .table td { font-size: 0.85rem; }
}
</style>
