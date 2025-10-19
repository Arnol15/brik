<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fahari Industrial & Agrisolution</title>
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./css/style.css">
  <script src="/js/main.js" defer></script>
</head>
<body class="bg-white dark:bg-gray-900 transition-colors">

  <!-- Navbar -->
  <?php include __DIR__ . "/partials/navbar.php"; ?>

  <!-- Hero Section -->
  <?php include __DIR__ . "/views/homeview.php"; ?>

  <!-- Solutions Section -->
  <?php include __DIR__ . "/views/solutions.php"; ?>

  <!-- Articles Section -->
  <?php include __DIR__ . "/views/articles.php"; ?>

  <!-- Features Section -->
  <?php include __DIR__ . "/views/features.php"; ?>

  <!-- Services Section -->
  <?php include __DIR__ . "/views/services.php"; ?>

  <!-- Socials Section -->
  <?php include __DIR__ . "/views/socials.php"; ?>

  <!-- Footer -->
  <?php include __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
