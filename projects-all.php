<?php
require_once __DIR__ . '/config/database.php';
$projects = mysqli_query($connection, "SELECT * FROM projects ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Projects | Brik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<?php include __DIR__ . '/partials/navbar.php'; ?>

<section class="py-16 px-4 md:px-12 lg:px-24">
    <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-[#014d3a] mb-2">Our Projects</h1>
        <p class="text-gray-600 dark:text-gray-300">Explore our engineering, automation, and integration projects</p>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (mysqli_num_rows($projects) > 0): ?>
            <?php while ($project = mysqli_fetch_assoc($projects)): ?>
                <div class="bg-white dark:bg-[#2a3b45] shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-[#014d3a] mb-2"><?= htmlspecialchars($project['name']) ?></h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            <?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...
                        </p>
                        <a href="#" class="inline-block bg-[#014d3a] text-white px-4 py-2 rounded-md text-sm hover:bg-[#013828]">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500 dark:text-gray-300 text-center col-span-full">No projects available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/infooter.php'; ?>

</body>
</html>
