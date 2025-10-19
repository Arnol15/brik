<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

$uploadDir = __DIR__ . '/../../uploads/projects/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $projectName = trim($_POST['project_name']);
    $description = trim($_POST['description']);

    // Fetch current project data
    $query = "SELECT * FROM projects WHERE id = $id";
    $result = mysqli_query($connection, $query);
    $project = mysqli_fetch_assoc($result);

    if (!$project) {
        $_SESSION['project-error'] = "Project not found.";
        header('Location: ../forms/manage-projects.php');
        exit;
    }

    // File uploads
    $fields = ['documentation', 'programs', 'schematics', 'bom', 'licenses'];
    $updates = [];

    foreach ($fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            // Delete old file
            if (!empty($project[$field]) && file_exists(__DIR__ . '/../../' . $project[$field])) {
                unlink(__DIR__ . '/../../' . $project[$field]);
            }

            // Save new file
            $fileTmp = $_FILES[$field]['tmp_name'];
            $fileName = $field . '-' . time() . '-' . basename($_FILES[$field]['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmp, $targetPath)) {
                $updates[$field] = 'uploads/projects/' . $fileName;
            }
        } else {
            $updates[$field] = $project[$field];
        }
    }

    // Update DB
    $stmt = mysqli_prepare($connection, "UPDATE projects 
        SET name=?, description=?, documentation=?, programs=?, schematics=?, bom=?, licenses=? 
        WHERE id=?");
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssi",
        $projectName,
        $description,
        $updates['documentation'],
        $updates['programs'],
        $updates['schematics'],
        $updates['bom'],
        $updates['licenses'],
        $id
    );
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['project-success'] = "✅ Project updated successfully!";
    } else {
        $_SESSION['project-error'] = "❌ Error updating project.";
    }

    header('Location: ../forms/manage-projects.php');
    exit;
}

// If accessed via GET (edit view)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($connection, "SELECT * FROM projects WHERE id=$id");
    $project = mysqli_fetch_assoc($result);
    if (!$project) {
        $_SESSION['project-error'] = "Project not found.";
        header('Location: ../forms/manage-projects.php');
        exit;
    }
} else {
    header('Location: ../forms/manage-projects.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 p-6">
<div class="max-w-3xl mx-auto bg-white dark:bg-[#2a3b45] shadow-md rounded-xl p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-white">Edit Project</h2>

    <form action="edit-project-logic.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="id" value="<?= $project['id'] ?>">

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project Name</label>
            <input type="text" name="project_name" value="<?= htmlspecialchars($project['name']) ?>" required
                   class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="4" required
                      class="w-full px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($project['description']) ?></textarea>
        </div>

        <?php
        $files = ['documentation', 'programs', 'schematics', 'bom', 'licenses'];
        foreach ($files as $file): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= ucfirst($file) ?></label>
                <?php if ($project[$file]): ?>
                    <p class="text-xs mb-1">
                        Current: <a href="../<?= $project[$file] ?>" target="_blank" class="text-green-600 hover:underline"><?= basename($project[$file]) ?></a>
                    </p>
                <?php endif; ?>
                <input type="file" name="<?= $file ?>" class="text-sm text-gray-500 file:bg-green-700 file:text-white file:py-1 file:px-3 rounded">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="w-full py-3 bg-[#014d3a] text-white rounded-md hover:bg-[#013828]">Update Project</button>
    </form>
</div>
</body>
</html>
