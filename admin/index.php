<?php
session_start();
require_once __DIR__ . '/../config/database.php';

//  Prevent cached access after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Session timeout in seconds (15 minutes)
$timeoutDuration = 900;

//  Redirect if not logged in
if (!isset($_SESSION['user-id'])) {
    header('Location: ../signin.php?session=expired');
    exit;
}

// ⏱️ Session expiration check
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutDuration) {
    session_unset();
    session_destroy();
    header("Location: ../signin.php?session=expired");
    exit;
}

// ✅ Update last activity timestamp
$_SESSION['last_activity'] = time();

// 👤 Fetch logged-in user info
$user_id = $_SESSION['user-id'];
$query = "SELECT id, username, email, avatar, is_admin FROM users WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// 🧭 Define user info safely
$username = htmlspecialchars($user['username'] ?? 'User');
$email = htmlspecialchars($user['email'] ?? 'user@example.com');
$avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : '../images/authors/default.png';
$isAdmin = (int)($user['is_admin'] ?? 0);

// 📄 Active page tracking
$activePage = $_GET['page'] ?? 'Dashboard';

// 📚 Sidebar navigation items
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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= htmlspecialchars($activePage) ?> - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
.sidebar-transition { transition: width .25s ease, transform .25s ease; }
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
html { transition: background-color 0.3s, color 0.3s; }
.sidebar-link { transition: background .15s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'dark') document.documentElement.classList.add('dark');
    else if (storedTheme === 'light') document.documentElement.classList.remove('dark');
    else if (window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark');
    updateDarkModeIcons();

    const sidebar = document.getElementById('sidebar');
    const wrapper = document.querySelector('.main-wrapper');
    const desktopToggleIcon = document.getElementById('desktopToggleIcon');

    if (localStorage.getItem('sidebar-expanded') === 'true') {
        sidebar.classList.add('expanded');
        wrapper.classList.add('sidebar-expanded');
        desktopToggleIcon.classList.replace('fa-chevron-right', 'fa-chevron-left');
    }

    // Auto logout after timeout
    setTimeout(() => {
        alert("Your session has expired due to inactivity. Please log in again.");
        window.location.href = 'logout.php';
    }, <?= $timeoutDuration * 1000 ?>);
});

function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateDarkModeIcons();
}
function updateDarkModeIcons() {
    const moon = document.querySelector('.dark-toggle .fa-moon');
    const sun = document.querySelector('.dark-toggle .fa-sun');
    moon.classList.toggle('hidden', document.documentElement.classList.contains('dark'));
    sun.classList.toggle('hidden', !document.documentElement.classList.contains('dark'));
}
function toggleSidebarDesktop() {
    const sidebar = document.getElementById('sidebar');
    const wrapper = document.querySelector('.main-wrapper');
    const icon = document.getElementById('desktopToggleIcon');
    const expanded = sidebar.classList.toggle('expanded');
    wrapper.classList.toggle('sidebar-expanded');
    icon.classList.toggle('fa-chevron-right');
    icon.classList.toggle('fa-chevron-left');
    localStorage.setItem('sidebar-expanded', expanded);
}
function toggleSidebarMobile() {
    const sidebar = document.getElementById('sidebar');
    const mobileIcon = document.getElementById('mobileToggleIcon');
    sidebar.classList.toggle('open');
    mobileIcon.classList.toggle('fa-chevron-up');
    mobileIcon.classList.toggle('fa-chevron-down');
}
</script>
</head>

<body class="bg-gray-100 dark:bg-[#1b2a2f] h-screen overflow-hidden relative flex">

<!-- 🧭 SIDEBAR -->
<aside id="sidebar" class="fixed top-0 left-0 bottom-0 z-40 flex flex-col bg-[#014d3a] dark:bg-[#22313a] text-white sidebar-transition">
  <div class="hidden md:flex items-center justify-end p-3 border-b border-green-900 dark:border-gray-700">
    <button id="desktopToggleBtn" onclick="toggleSidebarDesktop()" class="p-2 rounded-md hover:bg-green-800 transition">
      <i id="desktopToggleIcon" class="fa-solid fa-chevron-right"></i>
    </button>
  </div>

  <nav class="flex-1 mt-4 space-y-1 overflow-y-auto px-1">
    <?php foreach ($navLinks as $link):
        if (!empty($link['admin_only']) && !$isAdmin) continue;
        $isActive = ($activePage === $link['name']);
    ?>
      <a href="?page=<?= urlencode($link['name']) ?>"
         class="sidebar-link flex items-center gap-3 md:gap-4 px-3 py-3 rounded-md <?= $isActive ? 'bg-white text-[#014d3a] dark:bg-green-700 dark:text-white' : 'text-white hover:bg-green-800 dark:hover:bg-gray-700' ?>">
        <i class="<?= $link['icon'] ?> text-lg w-6 text-center"></i>
        <span class="nav-label text-sm font-medium"><?= htmlspecialchars($link['name']) ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- ✅ Sidebar Footer (Always Visible) -->
  <div class="sidebar-footer mt-auto p-4 border-t border-green-900 dark:border-gray-700 flex flex-col gap-3 sticky bottom-0 bg-[#014d3a] dark:bg-[#22313a]">
    <div class="flex items-center gap-3">
      <img src="<?= $avatar ?>" alt="User avatar" class="w-10 h-10 rounded-full border border-white/30 object-cover">
      <div class="flex flex-col truncate">
        <p class="text-sm font-medium"><?= $username ?></p>
        <p class="text-xs text-green-200 dark:text-gray-400 truncate"><?= $email ?></p>
      </div>
    </div>
    <div class="flex justify-between items-center mt-3">
      <button onclick="toggleDarkMode()" class="dark-toggle p-2 rounded-md hover:bg-green-800 dark:hover:bg-gray-600" title="Toggle Dark Mode">
        <i class="fa-solid fa-moon"></i>
        <i class="fa-solid fa-sun hidden"></i>
      </button>
      <a href="logout.php" class="p-2 rounded-md hover:bg-green-800 dark:hover:bg-gray-600" title="Logout">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>
  </div>
</aside>

<!-- 📱 MOBILE TOGGLE BUTTON -->
<button onclick="toggleSidebarMobile()" class="md:hidden fixed bottom-5 right-5 z-50 p-3 bg-[#014d3a] text-white rounded-full shadow-lg hover:bg-green-800 transition" aria-label="Toggle sidebar">
  <i id="mobileToggleIcon" class="fa-solid fa-chevron-up text-lg"></i>
</button>

<!-- 🧩 MAIN CONTENT -->
<div class="main-wrapper flex-1 flex flex-col transition-all duration-300">
  <header class="h-[72px] z-30 px-6 py-4 border-b bg-white dark:bg-[#2a3b45] border-gray-200 dark:border-gray-700 flex flex-col justify-center sticky top-0">
    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
      Dashboard / <span class="text-[#014d3a] dark:text-green-400"><?= htmlspecialchars($activePage) ?></span>
    </div>
    <h1 class="text-xl font-semibold text-gray-800 dark:text-white"><?= htmlspecialchars($activePage) ?></h1>
  </header>

  <main class="flex-1 overflow-y-auto p-6">
    <div class="bg-white dark:bg-[#2a3b45] rounded-xl shadow-md p-6 w-full">
      <?php
      $selectedFile = null;
      foreach ($navLinks as $link) {
          if ($link['name'] === $activePage) {
              if (!empty($link['admin_only']) && !$isAdmin) {
                  echo '<p class="text-red-500">You do not have permission to view this page.</p>';
                  $selectedFile = null;
                  break;
              }
              $selectedFile = __DIR__ . '/' . $link['file'];
              break;
          }
      }
      if ($selectedFile && file_exists($selectedFile)) include $selectedFile;
      else echo '<p class="text-gray-600 dark:text-gray-300">Select a section from the sidebar.</p>';
      ?>
    </div>
  </main>
</div>
</body>
</html>
