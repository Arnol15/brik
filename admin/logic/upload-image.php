<?php
require '../../config/database.php';

$targetDir = '../../images/posts/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (!empty($_FILES['file']['name'])) {
    $fileName = time() . '_' . basename($_FILES['file']['name']);
    $targetFile = $targetDir . $fileName;
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowed_ext)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image format.']);
        exit;
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo json_encode(['location' => ROOT_URL . 'images/posts/' . $fileName]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to move uploaded file']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
}
?>

