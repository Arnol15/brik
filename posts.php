<?php
include 'partials/navbar.php';
include 'postsData.php';

$slug = $_GET['slug'] ?? '';
$post = null;

foreach ($postsData as $p) {
    if ($p['slug'] === $slug) {
        $post = $p;
        break;
    }
}
?>

<main class="w-full bg-gray-50 dark:bg-gray-900 py-24 px-6 lg:px-20 transition-colors duration-300">
  <?php if ($post): ?>
    <article class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">

      <!-- Image with overlay -->
      <div class="relative w-full h-96">
        <img 
          src="<?= htmlspecialchars($post['image']) ?>" 
          alt="<?= htmlspecialchars($post['title']) ?>" 
          class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-gray-800/50 to-transparent"></div>

        <!-- Overlay content -->
        <div class="absolute bottom-6 left-6">
          <h1 class="text-3xl font-bold mb-4 text-slate-100 leading-snug tracking-wide drop-shadow-[0_2px_6px_rgba(0,0,0,0.8)]">
            <?= htmlspecialchars($post['title']) ?>
          </h1>
          
          <div class="flex items-center space-x-3 text-gray-100 backdrop-blur-sm bg-gray-900/30 px-3 py-2 rounded-full">
            <img 
              src="<?= htmlspecialchars($post['authorImage']) ?>" 
              alt="<?= htmlspecialchars($post['author']) ?>" 
              class="w-10 h-10 rounded-full border-2 border-slate-200 shadow-md"
            >
            <div>
              <p class="font-semibold text-slate-50"><?= htmlspecialchars($post['author']) ?></p>
              <p class="text-sm text-slate-300"><?= htmlspecialchars($post['date']) ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Post content -->
      <div class="p-10 md:p-12">
        <div class="prose max-w-none text-gray-800 dark:text-gray-200 leading-relaxed font-[Inter]">
          <style>
            .prose p {
              margin-bottom: 1.75rem !important;
              line-height: 1.95 !important;
              font-size: 1.15rem !important; /* slightly larger text for readability */
            }
            .prose h4, .prose h3 {
              margin-top: 2.2rem !important;
              margin-bottom: 1rem !important;
              font-size: 1.35rem !important;
              color: #1e293b; /* slate-800 */
              font-weight: 700;
            }
            .dark .prose h4, 
            .dark .prose h3 {
              color: #e2e8f0; /* slate-200 for dark mode */
            }
            .prose b, .prose strong {
              color: #0f172a; /* slate-900 */
              font-weight: 700;
            }
            .dark .prose b, 
            .dark .prose strong {
              color: #f1f5f9; /* slate-100 in dark mode */
            }
          </style>
          <?= html_entity_decode($post['content']) ?>
        </div>
      </div>
    </article>
  <?php else: ?>
    <div class="text-center text-gray-600 dark:text-gray-400 text-xl py-20">
      Post not found.
    </div>
  <?php endif; ?>
</main>

<?php include 'partials/infooter.php'; ?>
