<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM categories WHERE id=$id";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) === 1) {
        $category = mysqli_fetch_assoc($result);
    } else {
        echo "<p class='text-center text-red-600'>Category not found.</p>";
        exit;
    }
} else {
    echo "<p class='text-center text-red-600'>Invalid request.</p>";
    exit;
}
?>

<div class="bg-white dark:bg-[#2a3b45] shadow-md rounded-xl p-6 sm:p-8 w-full max-w-2xl mx-auto">
  <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-white text-center">Edit Category</h2>

  <form id="edit-category-form" class="space-y-6">
    <input type="hidden" name="id" value="<?= htmlspecialchars($category['id']) ?>">

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
      <input 
        type="text" 
        name="title" 
        value="<?= htmlspecialchars($category['title']) ?>" 
        required
        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-700"
      >
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
      <textarea 
        name="description" 
        rows="4"
        required
        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-700"
      ><?= htmlspecialchars($category['description']) ?></textarea>
    </div>

    <button 
      type="submit"
      class="w-full py-3 bg-green-700 text-white font-medium rounded-md hover:bg-green-800 transition"
    >
      Update Category
    </button>
  </form>
</div>

<script>
document.querySelector('#edit-category-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  const res = await fetch('../logic/edit-category-logic.php', {
    method: 'POST',
    body: formData
  });

  const result = await res.text();
  document.querySelector('#main-content').innerHTML = result; // reload updated categories
});
</script>
