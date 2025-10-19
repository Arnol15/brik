<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Get project record
    $query = "SELECT * FROM projects WHERE id = $id";
    $result = mysqli_query($connection, $query);
    $project = mysqli_fetch_assoc($result);

    if ($project) {
        // Delete files from server
        $files = ['documentation', 'programs', 'schematics', 'bom', 'licenses'];
        foreach ($files as $file) {
            if (!empty($project[$file]) && file_exists(__DIR__ . '/../../' . $project[$file])) {
                unlink(__DIR__ . '/../../' . $project[$file]);
            }
        }

        // Delete DB record
        $delete_query = "DELETE FROM projects WHERE id=$id LIMIT 1";
        if (mysqli_query($connection, $delete_query)) {
            $_SESSION['project-success'] = "✅ Project deleted successfully.";
        } else {
            $_SESSION['project-error'] = "❌ Failed to delete project from database.";
        }
    } else {
        $_SESSION['project-error'] = "Project not found.";
    }
} else {
    $_SESSION['project-error'] = "Invalid request.";
}

header('Location: ' . ROOT_URL . 'admin/forms/manage-projects.php');
exit;
?>
