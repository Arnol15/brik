<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Prevent cached access after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Session timeout (15 min)
$timeoutDuration = 900;
if (!isset($_SESSION['user-id'])) {
    header('Location: ../signin.php?session=expired');
    exit;
}
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutDuration) {
    session_unset();
    session_destroy();
    header("Location: ../signin.php?session=expired");
    exit;
}
$_SESSION['last_activity'] = time();

// Fetch user info
$user_id = $_SESSION['user-id'];
$query = "SELECT id, username, email, avatar, is_admin FROM users WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$username = htmlspecialchars($user['username'] ?? 'User');
$email = htmlspecialchars($user['email'] ?? 'user@example.com');
$avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : '../images/authors/default.png';
$isAdmin = (int)($user['is_admin'] ?? 0);

// Sidebar Navigation
$activePage = $_GET['page'] ?? 'Dashboard';
$navLinks = [
    ["name" => "Dashboard", "icon" => "fa-solid fa-gauge-high", "file" => "dashboard-home.php"],
    ["name" => "Add Post", "icon" => "fa-solid fa-pen-to-square", "file" => "forms/add-post.php"],
    ["name" => "Manage Posts", "icon" => "fa-solid fa-clipboard-list", "file" => "forms/manage-posts.php"],
    ["name" => "Add Project", "icon" => "fa-solid fa-briefcase", "file" => "forms/add-project.php"],
    ["name" => "Manage Projects", "icon" => "fa-solid fa-chart-bar", "file" => "forms/manage-projects.php"],
    ["name" => "Add Product", "icon" => "fa-solid fa-box", "file" => "forms/add-product.php", "admin_only" => true],
    ["name" => "Manage Products", "icon" => "fa-solid fa-boxes-stacked", "file" => "forms/manage-products.php", "admin_only" => true],
    ["name" => "Add User", "icon" => "fa-solid fa-user-plus", "file" => "forms/add-user.php", "admin_only" => true],
    ["name" => "Manage Users", "icon" => "fa-solid fa-users", "file" => "forms/manage-users.php", "admin_only" => true],
    ["name" => "Add Category", "icon" => "fa-solid fa-tag", "file" => "forms/add-category.php"],
    ["name" => "Manage Categories", "icon" => "fa-solid fa-folder-open", "file" => "forms/manage-categories.php"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($activePage) ?> - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
.sidebar-transition {
  transition: width .25s ease, transform .25s ease;
}
@media (min-width: 768px) {
  #sidebar { width: 84px; }
  #sidebar.expanded { width: 16rem; }
  .main-wrapper { margin-left: 84px; transition: margin-left .25s ease; }
  .main-wrapper.sidebar-expanded { margin-left: 16rem; }
  .nav-label { display: none; }
  #sidebar.expanded .nav-label { display: inline-block; }
}
@media (max-width: 767px) {
  #sidebar { width: 16rem; transform: translateX(-100%); }
  #sidebar.open { transform: translateX(0); }
}
</style>
</head>

<body class="bg-gray-100 dark:bg-[#1b2a2f] h-screen overflow-hidden flex relative">

<!-- SIDEBAR -->
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div id="mainWrapper" class="main-wrapper flex-1 flex flex-col transition-all duration-300">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="flex-1 overflow-hidden p-6">
  <div class="bg-white dark:bg-[#2a3b45] rounded-xl shadow-md p-6 w-full h-[calc(100vh-8rem)] overflow-hidden flex flex-col">
    
    <div class="flex-1 overflow-y-auto pr-2">
      <?php
      // Determine which page to show
      $selectedFile = null;
      foreach ($navLinks as $link) {
          if ($link['name'] === $activePage) {
              if (!empty($link['admin_only']) && !$isAdmin) {
                  echo '<p class="text-red-500 font-medium">You do not have permission to view this page.</p>';
                  $selectedFile = null;
                  break;
              }
              $selectedFile = __DIR__ . '/' . $link['file'];
              break;
          }
      }

      // Load the file if it exists, else show a message
      if ($selectedFile && file_exists($selectedFile)) {
          include $selectedFile;
      } else {
          echo '<p class="text-gray-600 dark:text-gray-300 text-center mt-10">Select a section from the sidebar to get started.</p>';
      }
      ?>
    </div>
  </div>
</main>


    </div>

<!-- DESKTOP SIDEBAR TOGGLE -->
<button onclick="toggleSidebar()" 
        class="hidden md:flex fixed bottom-6 right-6 z-50 p-3 bg-[#014d3a] text-white rounded-full shadow-lg hover:bg-green-800 transition"
        aria-label="Toggle sidebar">
  <i id="sidebarToggleIcon" class="fa-solid fa-chevron-right text-lg"></i>
</button>

<!-- MOBILE SIDEBAR TOGGLE -->
<button onclick="toggleSidebarMobile()" 
        class="md:hidden fixed bottom-6 right-6 z-50 p-3 bg-[#014d3a] text-white rounded-full shadow-lg hover:bg-green-800 transition"
        aria-label="Toggle mobile sidebar">
  <i id="mobileToggleIcon" class="fa-solid fa-chevron-up text-lg"></i>
</button>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const wrapper = document.getElementById('mainWrapper');
  const icon = document.getElementById('sidebarToggleIcon');
  sidebar.classList.toggle('expanded');
  wrapper.classList.toggle('sidebar-expanded');
  icon.classList.toggle('fa-chevron-right');
  icon.classList.toggle('fa-chevron-left');
}
function toggleSidebarMobile() {
  const sidebar = document.getElementById('sidebar');
  const icon = document.getElementById('mobileToggleIcon');
  sidebar.classList.toggle('open');
  icon.classList.toggle('fa-chevron-up');
  icon.classList.toggle('fa-chevron-down');
}
</script>

</body>
</html>
