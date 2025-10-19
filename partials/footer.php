<?php
require_once __DIR__ .'/../constants/statics.php';
?>

<footer class="flexCenter mb-0 bg-[#014d3a] dark:bg-[#0b1d18] text-white transition-colors duration-300 border-t border-green-700">
  <div class="padding-container max-container flex w-full flex-col gap-10 pt-8"> <!-- Added pt-8 for top spacing -->

    <!-- Top Section -->
    <div class="flex flex-col items-start justify-center gap-10 md:flex-row md:gap-[10%]">

      <!-- Logo -->
      <a href="/" class="mb-10 md:mb-0">
        <img src="./images/fahari_logo.png" alt="logo" width="90" height="40"
             class="dark:invert-0 rounded-md shadow-md bg-white p-1 dark:bg-gray-800 dark:p-2 transition" />
      </a>

      <!-- Footer Links -->
      <div class="flex flex-col sm:flex-row sm:flex-wrap gap-10 sm:justify-between md:flex-1">

        <!-- Dynamic Footer Links -->
        <?php foreach ($FOOTER_LINKS as $columns): ?>
          <div class="flex flex-col gap-5 min-w-[120px]">
            <h4 class="bold-18 whitespace-nowrap text-white">
              <?= htmlspecialchars($columns["title"]) ?>
            </h4>
            <ul class="regular-14 flex flex-col gap-3 text-gray-200">
              <?php foreach ($columns["links"] as $link): ?>
                <a href="/" class="hover:text-green-300 transition-colors duration-200">
                  <?= htmlspecialchars($link) ?>
                </a>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>

        <!-- Contact Info -->
        <div class="flex flex-col gap-5 min-w-[140px]">
          <h4 class="bold-18 whitespace-nowrap text-white">
            <?= htmlspecialchars($FOOTER_CONTACT_INFO["title"]) ?>
          </h4>
          <?php foreach ($FOOTER_CONTACT_INFO["links"] as $link): ?>
            <a href="/" class="flex flex-col sm:flex-row gap-2 lg:gap-4 text-gray-200 hover:text-green-300 transition-colors duration-200">
              <p class="whitespace-nowrap"><?= htmlspecialchars($link["label"]) ?>:</p>
              <p class="medium-14 whitespace-nowrap"><?= htmlspecialchars($link["value"]) ?></p>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Socials -->
        <div class="flex flex-col gap-5 min-w-[120px]">
          <h4 class="bold-18 whitespace-nowrap text-white">
            <?= htmlspecialchars($SOCIALS["title"]) ?>
          </h4>
          <ul class="regular-14 flex gap-6">
            <?php foreach ($SOCIALS["links"] as $link): ?>
              <a href="/" class="p-2 rounded-full bg-green-800/20 hover:bg-green-600/30 transition">
                <img src="<?= $link ?>" alt="social" width="20" height="20" class="mx-auto filter brightness-0 invert" />
              </a>
            <?php endforeach; ?>
          </ul>
        </div>

      </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-green-700 opacity-70 mt-2"></div>

    <!-- Copyright -->
    <div class="mb-4 w-full flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
      <p class="regular-14 text-gray-300">
        &#169; 2025 Fahari Industrial and Agrisolutions Limited. All rights reserved.
      </p>
      <div class="flex gap-6 text-sm">
        <a href="/" class="text-gray-300 hover:text-green-300 transition">Site Notice</a>
        <a href="/" class="text-gray-300 hover:text-green-300 transition">Data Protection</a>
        <a href="/" class="text-gray-300 hover:text-green-300 transition">Cookies</a>
      </div>
    </div>

  </div>
</footer>



