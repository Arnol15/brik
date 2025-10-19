<?php
session_start();
require_once __DIR__ . 'config/database.php'; // path to your DB connection

// Optional: show PHP errors during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow only logged-in users
if (!isset($_SESSION['user-id'])) {
    $_SESSION['error'] = "You must be logged in to add a project.";
    header('Location: ../index.php?page=Add Project');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $projectName = trim($_POST['project_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($projectName) || empty($description)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header('Location: ../index.php?page=Add Project');
        exit;
    }

    // Upload directory
    $uploadDir = __DIR__ . '/../uploads/projects/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Define allowed file sections
    $sections = ['documentation', 'programs', 'schematics', 'bom', 'licenses'];
    $uploadedFiles = [];

    foreach ($sections as $section) {
        $uploadedFiles[$section] = null;

        if (!empty($_FILES[$section]['name']) && $_FILES[$section]['error'] === UPLOAD_ERR_OK) {
            $fileTmp = $_FILES[$section]['tmp_name'];
            $fileName = basename($_FILES[$section]['name']);
            $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $fileName);
            $uniqueName = $section . '-' . time() . '-' . $safeName;
            $targetPath = $uploadDir . $uniqueName;

            if (move_uploaded_file($fileTmp, $targetPath)) {
                $uploadedFiles[$section] = 'uploads/projects/' . $uniqueName;
            }
        }
    }

    //  mysqli statement
    $query = "INSERT INTO projects 
              (name, description, documentation, programs, schematics, bom, licenses)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            'sssssss',
            $projectName,
            $description,
            $uploadedFiles['documentation'],
            $uploadedFiles['programs'],
            $uploadedFiles['schematics'],
            $uploadedFiles['bom'],
            $uploadedFiles['licenses']
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['add-project-success'] = "Project added successfully!";
        } else {
            $_SESSION['add-project-error'] = "Database insert failed: " . mysqli_error($connection);
        }
    } else {
        $_SESSION['add-project-error'] = "Database error: " . mysqli_error($connection);
    }

    header('Location: ../index.php?page=Add Project');
    exit;
} else {
    header('Location: ../index.php?page=Add Project');
    exit;
}
?>
