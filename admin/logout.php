<?php
session_start();

// ✅ Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// ✅ Prevent cached access
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// ✅ Redirect to sign-in page with a logout message
header("Location: /signin.php?logout=success");
exit;


