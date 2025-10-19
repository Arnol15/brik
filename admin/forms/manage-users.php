<?php
// Fetch users excluding the logged-in admin
$current_admin_id = $_SESSION['user-id'];
$query = "SELECT * FROM users WHERE NOT id=$current_admin_id";
$users = mysqli_query($connection, $query);
$usersArray = mysqli_fetch_all($users, MYSQLI_ASSOC);
?>

<section class="w-full px-4 py-6">
    <!-- Alert Messages -->
    <div class="max-w-5xl mx-auto mb-4 space-y-2">
        <?php if (isset($_SESSION['add-user-success'])): ?>
            <div class="p-3 rounded-md bg-green-100 text-green-800 border border-green-300 text-sm">
                <?= $_SESSION['add-user-success']; unset($_SESSION['add-user-success']); ?>
            </div>
        <?php elseif (isset($_SESSION['edit-user-success'])): ?>
            <div class="p-3 rounded-md bg-green-100 text-green-800 border border-green-300 text-sm">
                <?= $_SESSION['edit-user-success']; unset($_SESSION['edit-user-success']); ?>
            </div>
        <?php elseif (isset($_SESSION['edit-user'])): ?>
            <div class="p-3 rounded-md bg-red-100 text-red-800 border border-red-300 text-sm">
                <?= $_SESSION['edit-user']; unset($_SESSION['edit-user']); ?>
            </div>
        <?php elseif (isset($_SESSION['delete-user'])): ?>
            <div class="p-3 rounded-md bg-red-100 text-red-800 border border-red-300 text-sm">
                <?= $_SESSION['delete-user']; unset($_SESSION['delete-user']); ?>
            </div>
        <?php elseif (isset($_SESSION['delete-user-success'])): ?>
            <div class="p-3 rounded-md bg-green-100 text-green-800 border border-green-300 text-sm">
                <?= $_SESSION['delete-user-success']; unset($_SESSION['delete-user-success']); ?>
            </div>
        <?php endif; ?>
    </div>

    <!--  Main Manage Users Section -->
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-6">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-100">Manage Users</h2>
            <input
                type="text"
                id="searchInput"
                placeholder="Search users..."
                class="w-full sm:w-1/3 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-green-600 dark:bg-gray-700 dark:text-white"
            >
        </div>

        <?php if (count($usersArray) > 0): ?>
            <!--  Responsive Table Wrapper -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="p-3">Name</th>
                            <th class="p-3">Username</th>
                            <th class="p-3 text-center">Edit</th>
                            <th class="p-3 text-center">Delete</th>
                            <th class="p-3 text-center">Admin</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody" class="bg-white dark:bg-gray-800">
                        <?php foreach ($usersArray as $user): ?>
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="p-3 text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                    <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
                                </td>
                                <td class="p-3 text-gray-700 dark:text-gray-200"><?= htmlspecialchars($user['username']); ?></td>
                                <td class="p-3 text-center">
                                    <a href="<?= ROOT_URL ?>admin/edit-user.php?id=<?= $user['id']; ?>"
                                       class="inline-block px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-xs sm:text-sm">
                                        Edit
                                    </a>
                                </td>
                                <td class="p-3 text-center">
                                    <a href="<?= ROOT_URL ?>admin/delete-user.php?id=<?= $user['id']; ?>"
                                       class="inline-block px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-xs sm:text-sm">
                                        Delete
                                    </a>
                                </td>
                                <td class="p-3 text-center text-gray-800 dark:text-gray-100">
                                    <?= $user['is_admin'] ? '<span class="text-green-600 font-semibold">Yes</span>' : 'No'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!--  Pagination Controls -->
            <div class="flex flex-col sm:flex-row justify-center sm:justify-between items-center gap-3 mt-6">
                <button id="prevBtn"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md disabled:opacity-50 hover:bg-gray-300 dark:hover:bg-gray-500 transition"
                        disabled>Prev</button>
                <span id="pageIndicator" class="text-gray-700 dark:text-gray-300 text-sm">Page 1</span>
                <button id="nextBtn"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md disabled:opacity-50 hover:bg-gray-300 dark:hover:bg-gray-500 transition">Next</button>
            </div>
        <?php else: ?>
            <div class="p-6 text-center text-red-600 bg-red-50 dark:bg-gray-700 rounded-md">
                No users found.
            </div>
        <?php endif; ?>
    </div>
</section>

<!--  Search + Pagination Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const rows = Array.from(document.querySelectorAll("#userTableBody tr"));
    const searchInput = document.getElementById("searchInput");
    const itemsPerPage = 7;
    let currentPage = 1;

    const renderPage = () => {
        rows.forEach((row, index) => {
            row.style.display = (index >= (currentPage - 1) * itemsPerPage && index < currentPage * itemsPerPage)
                ? "" : "none";
        });
        document.getElementById("pageIndicator").textContent = `Page ${currentPage}`;
        document.getElementById("prevBtn").disabled = currentPage === 1;
        document.getElementById("nextBtn").disabled = currentPage * itemsPerPage >= rows.length;
    };

    document.getElementById("prevBtn").onclick = () => { if (currentPage > 1) { currentPage--; renderPage(); } };
    document.getElementById("nextBtn").onclick = () => { if (currentPage * itemsPerPage < rows.length) { currentPage++; renderPage(); } };

    searchInput.addEventListener("input", () => {
        const term = searchInput.value.toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? "" : "none";
        });
    });

    renderPage();
});
</script>
