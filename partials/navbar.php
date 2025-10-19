<?php
$nav_links = [
    ["key" => "home", "label" => "Home", "href" => "/"],
    ["key" => "Projects", "label" => "Projects", "href" => "./projects.php"],
    ["key" => "services", "label" => "Services", "href" => "/services"],
    ["key" => "contact", "label" => "Contact", "href" => "/contact"],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fahari Industrial and Agrisolutions Limited</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Enable Tailwind dark mode -->
  <script>
    tailwind.config = {
      darkMode: 'class',
    }
  </script>

  <style>
    /* Subtle shadow for navbar & smooth transitions */
    .nav-transition {
      transition: background-color 0.3s ease, color 0.3s ease;
    }
  </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-300">

  <!-- Navbar -->
  <nav class="flex justify-between items-center px-6 py-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md shadow-md fixed w-full top-0 z-[100] nav-transition">

    <!-- Logo -->
    <a href="/" class="flex items-center">
      <img src="./images/fahari_logo.png" alt="logo" class="w-14 h-14 object-contain dark:brightness-90">
    </a>

    <!-- Desktop Links -->
    <div class="hidden md:flex items-center gap-8">
      <?php foreach ($nav_links as $link): ?>
        <a href="<?= $link['href'] ?>" 
           class="text-gray-700 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-400 font-medium transition">
          <?= $link['label'] ?>
        </a>
      <?php endforeach; ?>

      <!-- Join Community Button -->
      <a href="signin.php" 
         class="ml-4 px-5 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg shadow transition flex items-center gap-2">
        <img src="./images/user.svg" alt="user" class="w-5 h-5 filter invert-0 dark:invert">
        Join Community
      </a>

      <!-- Dark Mode Toggle -->
      <button id="themeToggle" class="ml-3 p-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
        <img id="themeIcon" src="./images/moon.png" alt="theme" class="w-5 h-5">
      </button>
    </div>

    <!-- Mobile Menu Icon -->
    <button id="menuToggle" class="md:hidden p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
      <img src="./images/menu.svg" alt="menu" class="w-6 h-6 filter dark:invert">
    </button>
  </nav>

  <!-- Mobile Sidebar -->
  <div id="mobileMenu" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-2xl transform -translate-x-full transition-transform duration-300 z-[150]">
    <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
      <h2 class="text-lg font-bold dark:text-gray-100">Menu</h2>
      <button id="closeMenu">
        <img src="./images/close.svg" alt="close" class="w-6 h-6 filter dark:invert">
      </button>
    </div>

    <div class="flex flex-col gap-4 p-5">
      <?php foreach ($nav_links as $link): ?>
        <a href="<?= $link['href'] ?>" 
           class="text-gray-700 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-400 font-medium transition">
          <?= $link['label'] ?>
        </a>
      <?php endforeach; ?>

      <!-- Mobile Join Community Button -->
      <a href="signin.php" 
         class="mt-6 px-5 py-3 bg-green-700 hover:bg-green-800 text-white rounded-lg shadow transition flex items-center gap-2">
        <img src="./images/user.svg" alt="user" class="w-5 h-5 filter dark:invert">
        Join Community
      </a>

      <!-- Dark Mode Toggle (Mobile) -->
      <button id="themeToggleMobile" class="mt-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center gap-2 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
        <img id="themeIconMobile" src="./images/moon.png" alt="theme" class="w-5 h-5">
        Toggle Dark Mode
      </button>
    </div>
  </div>

  <!-- Overlay -->
  <div id="overlay" class="fixed inset-0 bg-black/50 hidden z-[140]"></div>

  <!-- Scripts -->
  <script>
    const menuToggle = document.getElementById("menuToggle");
    const mobileMenu = document.getElementById("mobileMenu");
    const overlay = document.getElementById("overlay");
    const closeMenu = document.getElementById("closeMenu");

    // Theme toggles
    const themeToggle = document.getElementById("themeToggle");
    const themeToggleMobile = document.getElementById("themeToggleMobile");
    const themeIcon = document.getElementById("themeIcon");
    const themeIconMobile = document.getElementById("themeIconMobile");

    // Load saved theme
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
      themeIcon.src = "./images/sun.png";
      themeIconMobile.src = "./images/sun.png";
    } else {
      document.documentElement.classList.remove('dark');
    }

    const toggleTheme = () => {
      document.documentElement.classList.toggle('dark');
      const isDark = document.documentElement.classList.contains('dark');
      localStorage.theme = isDark ? 'dark' : 'light';
      themeIcon.src = isDark ? "./images/sun.png" : "./images/moon.png";
      themeIconMobile.src = isDark ? "./images/sun.png" : "./images/moon.png";
    };

    themeToggle.addEventListener("click", toggleTheme);
    themeToggleMobile.addEventListener("click", toggleTheme);

    // Mobile menu open/close
    menuToggle.addEventListener("click", () => {
      mobileMenu.classList.remove("-translate-x-full");
      overlay.classList.remove("hidden");
    });

    closeMenu.addEventListener("click", () => {
      mobileMenu.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
    });

    overlay.addEventListener("click", () => {
      mobileMenu.classList.add("-translate-x-full");
      overlay.classList.add("hidden");
    });
  </script>
</body>
</html>

