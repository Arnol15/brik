<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../config/database.php';
?>

<!-- No DOCTYPE or <html> tags because this is embedded inside index.php -->

<div class="h-full flex flex-col">
  <!-- Title -->
  <h2 class="text-xl sm:text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
    Add Category
  </h2>

  <!-- Flash Messages -->
  <div id="flash-messages" class="space-y-2 mb-4">
    <?php
    $sessionMessages = [
        'add-category' => 'bg-red-100 text-red-600',
        'add-category-success' => 'bg-green-100 text-green-700',
    ];
    foreach ($sessionMessages as $key => $classes) {
        if (isset($_SESSION[$key])) {
            echo "<div class='p-3 rounded {$classes}'>" . $_SESSION[$key] . "</div>";
            unset($_SESSION[$key]);
        }
    }
    ?>
  </div>

  <!-- Form -->
  <form id="add-category-form" method="POST" class="space-y-4">
    <div>
      <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Title
      </label>
      <input type="text" name="title" id="title" required
             class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <div>
      <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Description
      </label>
      <textarea name="description" id="description" rows="3"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-green-700 text-white rounded-md text-sm font-medium hover:bg-green-800 transition">
        Add Category
      </button>
    </div>
  </form>
</div>

<!-- 🔻 AJAX Script -->
<script>
document.getElementById('add-category-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const flash = document.getElementById('flash-messages');

  try {
    const res = await fetch('logic/add-category-logic.php', {
      method: 'POST',
      body: formData
    });

    const text = await res.text();

    // Replace form section with server response for feedback
    flash.innerHTML = text.includes('successfully')
      ? `<div class="p-3 rounded bg-green-100 text-green-700">Category added successfully.</div>`
      : `<div class="p-3 rounded bg-red-100 text-red-600">${text}</div>`;

    // Clear form on success
    if (text.includes('successfully')) {
      form.reset();

      // Dispatch event for manage-category refresh
      document.dispatchEvent(new CustomEvent('categoryAdded'));
    }

  } catch (err) {
    flash.innerHTML = `<div class="p-3 rounded bg-red-100 text-red-600">Failed to add category. Try again.</div>`;
    console.error(err);
  }
});
</script>
