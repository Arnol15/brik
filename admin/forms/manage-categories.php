<?php
// Include database connection file
include_once __DIR__ . '/../../config/database.php'; // Adjust path based on your folder structure

// Fetch all categories from the database
$query = "SELECT * FROM categories ORDER BY title";
$categories = mysqli_query($connection, $query);
?>

<section class="dashboard">
    <?php
    // Session messages
    $sessionMessages = [
        'add-category' => 'error',
        'add-category-success' => 'success',
        'edit-category-success' => 'success',
        'edit-category' => 'error',
        'delete-category-success' => 'success'
    ];

    foreach ($sessionMessages as $key => $type) {
        if (isset($_SESSION[$key])) {
            echo '<div class="alert__message ' . $type . '"><p>' . $_SESSION[$key] . '</p></div>';
            unset($_SESSION[$key]);
        }
    }
    ?>

    <div class="container mx-auto p-4 sm:p-6">
        <main>
            <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-200">Manage Categories</h2>

            <?php if (mysqli_num_rows($categories) > 0): ?>
            <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <table class="w-full border-collapse text-sm sm:text-base">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-left font-semibold text-gray-700 dark:text-gray-200">
                            <th class="p-3 border-b border-gray-200 dark:border-gray-600">Title</th>
                            <th class="p-3 border-b border-gray-200 dark:border-gray-600">Description</th>
                            <th class="p-3 border-b border-gray-200 dark:border-gray-600 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-600">
                            <td class="p-3 border-b border-gray-200 dark:border-gray-600">
                                <?= htmlspecialchars($category['title']) ?>
                            </td>
                            <td class="p-3 border-b border-gray-200 dark:border-gray-600">
                                <?= htmlspecialchars($category['description']) ?>
                            </td>
                            <td class="p-3 border-b border-gray-200 dark:border-gray-600 text-center">
                                <div class="flex flex-col sm:flex-row justify-center gap-2">
                                    <a href="edit-category.php?id=<?= $category['id'] ?>"
                                       class="px-3 py-2 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-green-700 transition">Edit</a>
                                    <a href="delete-category.php?id=<?= $category['id'] ?>"
                                       class="px-3 py-2 bg-red-500 text-white text-xs sm:text-sm font-medium rounded-md hover:bg-red-600 transition">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert_message error mt-4 text-center text-gray-700 dark:text-gray-300">
                    No categories found
                </div>
            <?php endif; ?>
        </main>
    </div>
</section>

<style>
/* Responsive adjustments for mobile */
@media (max-width: 640px) {
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    th, td {
        white-space: normal; /* Allow text to wrap in mobile */
    }
}
</style>



