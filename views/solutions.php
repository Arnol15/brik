<?php
require_once __DIR__ . '/../config/database.php';

// Fetch latest 2 posts (featured first) with category & author info
$query = "
  SELECT 
    p.id, p.title, p.body, p.thumbnail, p.is_featured, p.created_at,
    c.id AS category_id, c.title AS category_title,
    u.id AS author_id, u.username AS author_name,
    u.avatar AS author_avatar
  FROM posts p
  JOIN categories c ON p.category_id = c.id
  JOIN users u ON p.author_id = u.id
  ORDER BY p.is_featured DESC, p.created_at DESC
  LIMIT 2
";
$result = mysqli_query($connection, $query);

$postsData = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $postsData[] = $row;
    }
}
?>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<section class="w-full bg-white dark:bg-gray-900 py-16 px-4 sm:px-6 lg:px-20 transition-colors duration-300">
  <?php if (count($postsData) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
      <?php foreach ($postsData as $post): 
        // Thumbnail
        $thumbPath = "." . htmlspecialchars($post['thumbnail']);
        if (!file_exists($thumbPath) || empty($post['thumbnail'])) {
            $thumbPath = "./images/posts/default-post.jpg";
        }

        // Author Avatar
        $avatarPath = "./admin/images/authors/" . htmlspecialchars($post['author_avatar']);
        if (empty($post['author_avatar']) || !file_exists($avatarPath)) {
        $avatarPath = "./admin/images/authors/default-avatar.png";
      }

      ?>
        <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col hover:shadow-2xl transition-shadow duration-300">
          
          <!-- 🖼 Thumbnail -->
          <div class="relative w-full h-64 sm:h-72 overflow-hidden">
            <img 
              src="<?= $thumbPath ?>" 
              alt="<?= htmlspecialchars($post['title']) ?>" 
              class="object-cover w-full h-full transition-transform duration-300 hover:scale-105"
              loading="lazy"
            >

            <?php if ($post['is_featured']): ?>
              <span class="absolute top-3 left-3 bg-green-600 text-white text-xs px-3 py-1 rounded-full uppercase tracking-wider shadow-md">
                Featured
              </span>
            <?php endif; ?>

            <!-- 👤 Author Overlay -->
            <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 via-black/30 to-transparent text-white px-4 py-3 flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <img 
                  src="<?= $avatarPath ?>" 
                  alt="<?= htmlspecialchars($post['author_name']) ?>" 
                  class="w-10 h-10 rounded-full border-2 border-white object-cover shadow-md"
                >
                <div>
                  <p class="text-sm font-semibold"><?= htmlspecialchars($post['author_name']) ?></p>
                  <p class="text-xs text-gray-300"><?= date('M j, Y', strtotime($post['created_at'])) ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- 📝 Post Content -->
          <div class="p-6 flex flex-col flex-1">
            <!-- Category Tag -->
            <a 
              href="category-posts.php?id=<?= urlencode($post['category_id']) ?>" 
              class="inline-block text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/40 
                     text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider hover:bg-green-100 
                     dark:hover:bg-green-800 transition duration-300 w-max"
            >
              <?= htmlspecialchars($post['category_title']) ?>
            </a>
            
            <h3 class="mt-3 text-gray-800 dark:text-white text-lg sm:text-xl font-semibold leading-snug hover:text-green-600 dark:hover:text-green-400 transition-colors line-clamp-2">
              <?= htmlspecialchars($post['title']) ?>
            </h3>

            <?php
              $body = strip_tags($post['body']);
              $words = preg_split('/\s+/', $body);
              $short = implode(' ', array_slice($words, 0, 50)) . (count($words) > 50 ? '...' : '');
            ?>

            <p class="mt-3 text-gray-700 dark:text-gray-300 text-base flex-1 leading-relaxed">
              <?= htmlspecialchars($short) ?>
            </p>

            <!-- 🔗 Discover More -->
            <a 
              href="post-details.php?id=<?= urlencode($post['id']) ?>" 
              class="mt-6 inline-block w-max px-6 py-3 rounded-full border border-gray-400 bg-green-700  
                     text-gray-100 dark:text-gray-300 hover:bg-green-500 hover:text-black 
                     transition font-medium shadow-sm hover:shadow-md"
            >
              Discover More
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-16 text-gray-600 dark:text-gray-300">
      <p class="text-lg font-medium">No posts available yet.</p>
    </div>
  <?php endif; ?>
</section>
