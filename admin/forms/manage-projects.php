<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

// --- Handle Search & Filter ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int) $_GET['category'] : 0;

// --- Fetch categories for dropdown (optional) ---
$catQuery = "SELECT id, title FROM categories ORDER BY title";
$categories = mysqli_query($connection, $catQuery);

// --- Build query dynamically ---
$query = "SELECT * FROM projects WHERE 1=1";
if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($connection, $search);
    $query .= " AND (name LIKE '%$searchEscaped%' OR description LIKE '%$searchEscaped%')";
}
if ($categoryFilter > 0) {
    $query .= " AND category_id = $categoryFilter";
}
$query .= " ORDER BY created_at DESC";

$result = mysqli_query($connection, $query);
?>

<section class="dashboard">

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['project-success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $_SESSION['project-success']; unset($_SESSION['project-success']); ?>
        </div>
    <?php elseif (isset($_SESSION['project-error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $_SESSION['project-error']; unset($_SESSION['project-error']); ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Manage Projects</h2>
        <a href="?page=Add Project"
           class="bg-[#014d3a] hover:bg-[#013828] text-white py-2 px-4 rounded-md shadow whitespace-nowrap">
            + Add New Project
        </a>
    </div>

    <!-- Search and Filter -->
    <form method="GET" class="mb-6 flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <input type="hidden" name="page" value="Manage Projects">

        <input
            type="text"
            name="search"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search projects..."
            class="flex-1 w-full sm:w-auto px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white focus:ring focus:ring-green-300"
        >

        <select name="category"
                class="px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white focus:ring focus:ring-green-300">
            <option value="0">All Categories</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit"
                class="bg-[#014d3a] hover:bg-[#013828] text-white py-2 px-6 rounded-md shadow">
            Filter
        </button>
    </form>

    <!-- Project Table -->
    <div class="overflow-x-auto bg-white dark:bg-[#2a3b45] rounded-xl shadow-md p-4">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Project Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">Files</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $i = 1; while ($project = mysqli_fetch_assoc($result)): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300"><?= $i++ ?></td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($project['name']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars(substr($project['description'], 0, 80)) ?>...</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hidden sm:table-cell">
                                <?php
                                $files = ['documentation', 'programs', 'schematics', 'bom', 'licenses'];
                                foreach ($files as $file) {
                                    if (!empty($project[$file])) {
                                        echo "<a href='../" . htmlspecialchars($project[$file]) . "' target='_blank' class='text-green-700 dark:text-green-400 hover:underline mr-2'>" . ucfirst($file) . "</a>";
                                    }
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <a href="../logic/edit-project-logic.php?id=<?= $project['id'] ?>"
                                   class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="../logic/delete-project-logic.php?id=<?= $project['id'] ?>"
                                   onclick="return confirm('Are you sure you want to delete this project?')"
                                   class="text-red-600 hover:text-red-800">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-6 text-gray-500 dark:text-gray-400">No matching projects found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
