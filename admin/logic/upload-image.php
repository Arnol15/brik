<?php
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if missing
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- JSON header for all responses ---
header('Content-Type: application/json');

// --- Security Checks ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// --- Validate CSRF token ---
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit;
}

// --- Check if file uploaded ---
if (!isset($_FILES['file']['tmp_name']) || empty($_FILES['file']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No file received.']);
    exit;
}

$file = $_FILES['file'];
$tmp_name = $file['tmp_name'];
$original_name = basename($file['name']);

// --- Prepare upload directory ---
$uploadDir = realpath(__DIR__ . '/../../images/posts/');
if (!$uploadDir) {
    mkdir(__DIR__ . '/../../images/posts/', 0777, true);
    $uploadDir = realpath(__DIR__ . '/../../images/posts/');
}

// --- Validate file size ---
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'File too large (max 5MB).']);
    exit;
}

// --- Validate file type ---
$allowed_mimes = [
    'image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp', 'image/svg+xml'
];
$file_info = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($file_info, $tmp_name);
finfo_close($file_info);

if (!in_array($mime_type, $allowed_mimes)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid image type.']);
    exit;
}

// --- Secure and unique file name ---
$extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
$sanitized_name = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($original_name, PATHINFO_FILENAME));
$new_file_name = time() . '_' . $sanitized_name . '.' . $extension;
$destination = $uploadDir . DIRECTORY_SEPARATOR . $new_file_name;

// --- Move uploaded file ---
if (move_uploaded_file($tmp_name, $destination)) {
    // Return relative path for frontend use
    $public_path = '/images/posts/' . $new_file_name;
    echo json_encode(['status' => 'success', 'location' => $public_path]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save uploaded image.']);
    exit;
}
?>
