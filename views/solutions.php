<?php
// Connect to your root-level database config (adjust if needed)
require_once __DIR__ . '/../config/database.php';

// 📰 Fetch only 2 latest posts (featured first)
$query = "
  SELECT id, title, body, thumbnail, category_id, author_id, is_featured, created_at 
  FROM posts 
  ORDER BY is_featured DESC, created_at DESC
  LIMIT 2
";
$result = mysqli_query($connection, $query);

// Convert results to array
$postsData = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $postsData[] = $row;
    }
}
?>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<section class="w-full bg-white dark:bg-gray-900 py-16 px-4 sm:px-6 lg:px-20 transition-colors">
  <?php if (count($postsData) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <?php foreach ($postsData as $post): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col transition-colors hover:shadow-xl duration-300">
          
          <!-- 🖼️ Thumbnail -->
          <div class="relative w-full h-64 sm:h-72">
            <img 
              src="<?= htmlspecialchars($post['thumbnail']) ?>" 
              alt="<?= htmlspecialchars($post['title']) ?>" 
              class="object-cover w-full h-full transition-transform duration-300 hover:scale-105"
              onerror="this.src='../images/default-post.jpg';" 
            >
            <?php if ($post['is_featured']): ?>
              <span class="absolute top-3 left-3 bg-green-600 text-white text-xs px-3 py-1 rounded-full uppercase tracking-wider">
                Featured
              </span>
            <?php endif; ?>
          </div>

          <!-- 📝 Post Content -->
          <div class="p-6 flex flex-col flex-1">
            <span class="text-green-600 dark:text-green-400 uppercase tracking-wide text-sm font-semibold">
              Solution Spotlight
            </span>
            
            <h3 class="mt-3 text-gray-800 dark:text-white text-lg sm:text-xl font-semibold leading-snug">
              <?= htmlspecialchars($post['title']) ?>
            </h3>

            <?php
              // ✂️ Limit body preview to 50 words
              $body = strip_tags($post['body']);
              $words = explode(' ', $body);
              $short = implode(' ', array_slice($words, 0, 50)) . (count($words) > 50 ? '...' : '');
            ?>

            <p class="mt-3 text-gray-700 dark:text-gray-300 text-base flex-1 leading-relaxed">
              <?= nl2br(htmlspecialchars($short)) ?>
            </p>

            <!-- 🔗 Discover More -->
            <a 
              href="post-details.php?id=<?= urlencode($post['id']) ?>" 
              class="mt-6 w-max px-6 py-3 rounded-full border border-gray-400 text-gray-800 dark:text-gray-300 
                     hover:bg-green-500 hover:text-black transition font-medium"
            >
              Discover More
            </a>
          </div>

          <!-- 📅 Post Meta -->
          <div class="px-6 pb-4 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
            Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-16 text-gray-600 dark:text-gray-300">
      <p class="text-lg">No posts available yet.</p>
    </div>
  <?php endif; ?>
</section>

