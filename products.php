<?php
// Example dynamic product data (replace this with DB query)
$products = [
  [
    'code' => 'AL1400',
    'designation' => 'IO-Link Master PerformanceLine',
    'interface' => 'PROFINET',
    'protection' => ['IP65', 'IP66', 'IP67'],
    'revision' => '1.1',
    'price' => '399.50 €',
    'image' => 'assets/images/al1400.png'
  ],
  [
    'code' => 'AL1402',
    'designation' => 'IO-Link Master PerformanceLine',
    'interface' => 'EtherNet/IP',
    'protection' => ['IP65', 'IP66', 'IP67'],
    'revision' => '1.1',
    'price' => '389.90 €',
    'image' => 'assets/images/al1402.png'
  ],
];
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fahari Product Catalog</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            fahariGreen: '#1db954',
            fahariDark: '#0e1a13',
          }
        }
      }
    }
  </script>
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-fahariGreen">Fahari Products</h1>

    <div class="flex items-center gap-3">
      <input type="text" placeholder="Search..." class="px-3 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 w-40 md:w-64">
      <button id="toggleFilters" class="md:hidden bg-fahariGreen text-white px-3 py-2 rounded-lg font-semibold">
        Filters
      </button>
    </div>
  </header>

  <!-- Main Layout -->
  <main class="flex flex-col md:flex-row relative overflow-hidden">

    <!-- Sliding Sidebar -->
    <aside id="filterSidebar" class="fixed md:static top-0 left-0 w-3/4 max-w-sm md:w-1/4 h-full md:h-auto bg-gray-50 dark:bg-gray-950 border-r dark:border-gray-800 p-6 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40">
      <div class="flex justify-between items-center mb-6 md:hidden">
        <h2 class="text-lg font-semibold">Filters</h2>
        <button id="closeFilters" class="text-gray-500 dark:text-gray-300 text-2xl">&times;</button>
      </div>

      <details open class="mb-4">
        <summary class="font-semibold cursor-pointer text-fahariGreen">Communication Interface</summary>
        <div class="mt-3 space-y-2 text-sm">
          <label class="flex items-center gap-2"><input type="checkbox"> EtherNet/IP</label>
          <label class="flex items-center gap-2"><input type="checkbox"> IO-Link</label>
          <label class="flex items-center gap-2"><input type="checkbox"> PROFINET</label>
        </div>
      </details>

      <details class="mb-4">
        <summary class="font-semibold cursor-pointer text-fahariGreen">Protection</summary>
        <div class="mt-3 space-y-2 text-sm">
          <label class="flex items-center gap-2"><input type="checkbox"> IP65</label>
          <label class="flex items-center gap-2"><input type="checkbox"> IP66</label>
          <label class="flex items-center gap-2"><input type="checkbox"> IP67</label>
        </div>
      </details>
    </aside>

    <!-- Overlay for mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-30 md:hidden"></div>

    <!-- Product List -->
    <section class="flex-1 p-6 md:ml-0">
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Applicable products: <?= count($products) ?></p>

      <div class="grid gap-6">
        <?php foreach ($products as $product): ?>
          <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 shadow-sm hover:shadow-md transition-shadow">

            <!-- Product Info -->
            <div class="flex items-center gap-4 w-full md:w-auto">
              <img src="<?= $product['image'] ?>" alt="<?= $product['code'] ?>" class="w-16 h-16 object-contain">
              <div>
                <h3 class="font-bold text-lg text-fahariGreen"><?= $product['code'] ?></h3>
                <p class="text-sm text-gray-600 dark:text-gray-300"><?= $product['designation'] ?></p>
                <p class="text-xs text-gray-400">Interface: <?= $product['interface'] ?></p>
              </div>
            </div>

            <!-- Product Details -->
            <div class="flex flex-col md:flex-row md:items-center gap-6 w-full md:w-auto justify-between">
              <div class="text-sm space-y-1">
                <p><span class="font-medium">Protection:</span> <?= implode(', ', $product['protection']) ?></p>
                <p><span class="font-medium">IO-Link Rev:</span> <?= $product['revision'] ?></p>
              </div>

              <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400">List price:</p>
                <p class="text-xl font-semibold text-fahariGreen"><?= $product['price'] ?></p>
              </div>

              <button class="bg-fahariGreen hover:bg-green-600 text-white font-medium px-5 py-2 rounded-xl transition">
                Add to Cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <!-- Dark Mode Toggle -->
  <button 
    onclick="document.documentElement.classList.toggle('dark')" 
    class="fixed bottom-6 right-6 bg-fahariGreen text-white p-3 rounded-full shadow-lg hover:bg-green-600 transition"
  >
    🌙
  </button>

  <!-- Sidebar Script -->
  <script>
    const sidebar = document.getElementById('filterSidebar');
    const overlay = document.getElementById('overlay');
    const toggleFilters = document.getElementById('toggleFilters');
    const closeFilters = document.getElementById('closeFilters');

    toggleFilters.addEventListener('click', () => {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    });

    closeFilters.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    });
  </script>

</body>
</html>
