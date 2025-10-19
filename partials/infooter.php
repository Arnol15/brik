<footer class="bg-[#014d3a] dark:bg-[#0b1d18] text-white mt-20 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-10">
    
    <!-- About Section -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:space-x-5">
      <img 
        src="./images/fahari_logo.png" 
        alt="Fahari Logo" 
        class="w-16 h-16 mb-4 sm:mb-0 rounded-md shadow-md bg-white p-1 dark:bg-gray-800 dark:p-2 transition"
      >
      <div>
        <h2 class="text-xl font-semibold mb-3 text-white">About Fahari</h2>
        <p class="text-gray-200 leading-relaxed text-sm sm:text-base">
          Fahari Blog provides insights for engineers and innovators in 
          automation, control systems, and electromechanical technology.
        </p>
      </div>
    </div>

    <!-- Quick Links -->
    <div>
      <h2 class="text-xl font-semibold mb-4 text-white">Quick Links</h2>
      <ul class="space-y-2 text-gray-200 text-sm sm:text-base">
        <li><a href="index.php" class="hover:text-green-300 transition">Home</a></li>
        <li><a href="#posts" class="hover:text-green-300 transition">Blog</a></li>
        <li><a href="contact.php" class="hover:text-green-300 transition">Contact</a></li>
      </ul>
    </div>

    <!-- Newsletter -->
    <div>
      <h2 class="text-xl font-semibold mb-4 text-white">Stay Updated</h2>
      <form class="flex flex-col gap-3">
        <input 
          type="email" 
          placeholder="Enter your email"
          class="px-4 py-2 rounded-md text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 
                     focus:outline-none focus:ring-2 focus:ring-green-500 placeholder-gray-500 dark:placeholder-gray-300"
        >
        <button 
          type="submit"
          class="bg-green-600 hover:bg-green-700 transition rounded-md px-4 py-2 text-white font-medium"
        >
          Subscribe
        </button>
      </form>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="border-t border-green-800 mt-6 text-center py-4 text-xs sm:text-sm text-gray-300 dark:text-gray-400">
    © <?= date('Y') ?> <b>Fahari Blog</b> — Built for Engineers & Innovators.
  </div>
</footer>

