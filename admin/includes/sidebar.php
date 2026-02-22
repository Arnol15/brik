<?php
require '../config/database.php'; // adjust path if needed

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Ensure variables are available when included
if (!isset($navLinks)) $navLinks = [];
if (!isset($activePage)) $activePage = 'Dashboard';
$isAdmin = 0;
$username = 'User';
$avatar = '../images/authors/default.png';

//  Fetch user info directly from database if logged in
if (isset($_SESSION['user-id'])) {
    $user_id = $_SESSION['user-id'];
    $query = "SELECT username, avatar, is_admin FROM users WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $username = htmlspecialchars($user['username']);
        $avatar = '../images/authors/' . ($user['avatar'] ?: 'default.png');
        $isAdmin = $user['is_admin'];
    }
}
?>
<aside id="sidebar"
       class="sidebar-transition bg-[#014d3a] text-white dark:bg-[#12211b]
              h-screen fixed top-0 left-0 z-40 flex flex-col overflow-hidden">

  <!-- 🧑‍💼 USER INFO -->
  <div class="flex items-center gap-3 px-4 py-5 border-b border-green-800">
    <img src="<?= $avatar ?>" alt="User Avatar"
         class="w-10 h-10 rounded-full border border-green-700 object-cover">
    <span class="font-semibold text-white text-sm nav-label"><?= htmlspecialchars($username) ?></span>
  </div>

  <!-- 📋 NAVIGATION LINKS -->
  <nav class="flex-1 overflow-y-auto mt-2">
    <ul class="space-y-1">
      <?php foreach ($navLinks as $link): ?>
        <?php if (!empty($link['admin_only']) && !$isAdmin) continue; ?>
        <li>
          <a href="index.php?page=<?= urlencode($link['name']) ?>"
             class="flex items-center gap-3 px-4 py-2 text-sm font-medium rounded-lg
                    transition-colors duration-200
                    <?= $activePage === $link['name']
                        ? 'bg-white text-[#014d3a] dark:bg-green-800 dark:text-white'
                        : 'text-white hover:bg-green-700 hover:text-white dark:hover:bg-green-700' ?>">
            <i class="<?= $link['icon'] ?> text-lg min-w-[20px] text-current"></i>
            <span class="nav-label"><?= htmlspecialchars($link['name']) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <!-- 🚪 LOGOUT -->
  <div class="mt-auto border-t border-green-800 p-4">
    <a href="../logout.php"
       class="flex items-center gap-3 text-sm text-white hover:text-green-300 transition-colors duration-200">
      <i class="fa-solid fa-right-from-bracket text-lg"></i>
      <span class="nav-label">Logout</span>
    </a>
  </div>
</aside>

<style>
/* Sidebar expand/collapse + responsive behavior */
.sidebar-transition {
  transition: width .25s ease, transform .25s ease;
}

/* Desktop behavior */
@media (min-width: 768px) {
  #sidebar { width: 84px; }
  #sidebar.expanded { width: 16rem; }
  .main-wrapper { margin-left: 84px; transition: margin-left .25s ease; }
  .main-wrapper.sidebar-expanded { margin-left: 16rem; }

  /* Hide labels when collapsed */
  .nav-label { display: none; }
  #sidebar.expanded .nav-label { display: inline-block; }
}

/* Mobile behavior */
@media (max-width: 767px) {
  #sidebar {
    width: 16rem;
    transform: translateX(-100%);
  }
  #sidebar.open { transform: translateX(0); }
}
</style>
