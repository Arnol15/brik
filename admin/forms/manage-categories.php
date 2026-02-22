<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../config/database.php';

// Fetch categories
$query = "SELECT * FROM categories ORDER BY title";
$categories = mysqli_query($connection, $query);
?>

<!-- No DOCTYPE or <html> tags because this is embedded inside index.php -->

<div class="h-full flex flex-col">
  <!-- Title -->
  <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
    Manage Categories
  </h2>

  <!-- Flash Messages -->
  <div class="space-y-2 mb-4">
    <?php
    $sessionMessages = [
        'add-category' => 'bg-red-100 text-red-600',
        'add-category-success' => 'bg-green-100 text-green-700',
        'edit-category' => 'bg-red-100 text-red-600',
        'edit-category-success' => 'bg-green-100 text-green-700',
        'delete-category-success' => 'bg-green-100 text-green-700',
    ];
    foreach ($sessionMessages as $key => $classes) {
        if (isset($_SESSION[$key])) {
            echo "<div class='p-3 rounded {$classes}'>" . $_SESSION[$key] . "</div>";
            unset($_SESSION[$key]);
        }
    }
    ?>
  </div>

  <!-- Table Wrapper -->
  <div class="flex-1 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <?php if (mysqli_num_rows($categories) > 0): ?>
      <table class="w-full text-sm sm:text-base border-collapse">
        <thead>
          <tr class="bg-gray-100 dark:bg-gray-700 text-left font-semibold text-gray-700 dark:text-gray-200">
            <th class="p-3 border-b border-gray-200 dark:border-gray-600">Title</th>
            <th class="p-3 border-b border-gray-200 dark:border-gray-600">Description</th>
            <th class="p-3 border-b border-gray-200 dark:border-gray-600 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($category = mysqli_fetch_assoc($categories)): ?>
          <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
            <td class="p-3 border-b border-gray-200 dark:border-gray-600 break-words">
              <?= htmlspecialchars($category['title']) ?>
            </td>
            <td class="p-3 border-b border-gray-200 dark:border-gray-600 break-words">
              <?= htmlspecialchars($category['description']) ?>
            </td>
            <td class="p-3 border-b border-gray-200 dark:border-gray-600 text-center">
              <div class="flex flex-wrap justify-center gap-2">
                <a href="edit-category.php?id=<?= $category['id'] ?>"
                   class="px-3 py-2 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-green-700 transition">
                   Edit
                </a>
                <a href="#" 
                   onclick="deleteCategory(<?= $category['id'] ?>, '<?= addslashes($category['title']) ?>'); return false;"
                   class="px-3 py-2 bg-red-500 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-red-600 transition">
                   Delete
                </a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-gray-700 dark:text-gray-300 text-center p-4">No categories found.</p>
    <?php endif; ?>
  </div>
</div>

<!-- 🔻 AJAX Script Block -->
<script>
async function deleteCategory(id, title) {
  if (!confirm(`Are you sure you want to delete the category "${title}"? This action cannot be undone.`)) return;

  const formData = new FormData();
  formData.append('id', id);

  try {
    const res = await fetch('../logic/delete-category.php', {
      method: 'POST',
      body: formData
    });

    const result = await res.text();
    document.querySelector('#main-content').innerHTML = result;
  } catch (err) {
    alert('Failed to delete category. Please try again.');
    console.error(err);
  }
}

// Hook: Re-render after adding category via AJAX (optional future expansion)
document.addEventListener('categoryAdded', async () => {
  try {
    const res = await fetch('../forms/manage-category.php');
    const html = await res.text();
    document.querySelector('#main-content').innerHTML = html;
  } catch (err) {
    console.error('Failed to refresh category list:', err);
  }
});
</script>
