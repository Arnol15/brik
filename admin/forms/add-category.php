<?php


// Restore data if redirected back
$title = $_SESSION['add-category-data']['title'] ?? '';
$description = $_SESSION['add-category-data']['description'] ?? '';
unset($_SESSION['add-category-data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Category</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

  <div class="bg-white shadow-md rounded-xl p-6 sm:p-8 w-full max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800 text-center">Add New Category</h2>

    <!-- Error -->
    <?php if (isset($_SESSION['add-category'])): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-600 rounded-md">
        <?= $_SESSION['add-category']; unset($_SESSION['add-category']); ?>
      </div>
    <?php endif; ?>

    <!-- Success -->
    <?php if (isset($_SESSION['add-category-success'])): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
        <?= $_SESSION['add-category-success']; unset($_SESSION['add-category-success']); ?>
      </div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form 
      action="logic/add-category-logic.php" 
      method="POST" 
      class="space-y-6"
    >
      <!-- Category Title -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Category Title</label>
        <input 
          type="text" 
          name="title" 
          value="<?= htmlspecialchars($title) ?>" 
          placeholder="Enter category title"
          required
          class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-700"
        >
      </div>

      <!-- Description -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea 
          name="description" 
          rows="4" 
          placeholder="Enter category description..."
          required
          class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-700"
        ><?= htmlspecialchars($description) ?></textarea>
      </div>

      <!-- Submit Button -->
      <button 
        type="submit" 
        name="submit"
        class="w-full py-3 bg-green-700 text-white font-medium rounded-md hover:bg-green-800 transition"
      >
        Add Category
      </button>
    </form>
  </div>
</body>
</html>

