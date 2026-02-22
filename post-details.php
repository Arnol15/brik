<?php 
require_once __DIR__ . '/config/database.php';

// --- Validate and fetch post ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];
$query = "SELECT p.*, u.username AS author_name, u.avatar AS author_avatar 
          FROM posts p
          LEFT JOIN users u ON p.author_id = u.id
          WHERE p.id = $id
          LIMIT 1";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit;
}

$post = mysqli_fetch_assoc($result);

// --- Related Posts ---
$category_id = (int)$post['category_id'];
$relatedQuery = "
  SELECT id, title, thumbnail, created_at 
  FROM posts 
  WHERE category_id = $category_id AND id != $id
  ORDER BY created_at DESC
  LIMIT 3
";
$relatedResult = mysqli_query($connection, $relatedQuery);
$relatedPosts = $relatedResult ? mysqli_fetch_all($relatedResult, MYSQLI_ASSOC) : [];

// Build post URL for sharing
$baseUrl = "https://fahariai.com"; // change to your domain
$postUrl = "$baseUrl/post-details.php?id=" . urlencode($post['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($post['title']) ?> | Fahari Solutions</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./css/style.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

  <style>
    /* Smooth gradient overlay for author info */
    .author-gradient {
      background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.45) 70%, rgba(0,0,0,0.1) 100%);
    }
  </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

  <!-- Post Section -->
  <section class="max-w-5xl mx-auto px-5 py-16">

    <!-- Thumbnail -->
    <div class="relative rounded-2xl overflow-hidden shadow-lg">
      <img 
        src="<?= htmlspecialchars('.' . $post['thumbnail']) ?>" 
        alt="<?= htmlspecialchars($post['title']) ?>" 
        class="w-full h-[28rem] md:h-[34rem] object-cover hover:scale-105 transition-transform duration-500 ease-in-out"
        onerror="this.src='./images/posts/default-post.jpg';"
      >

      <?php if ($post['is_featured']): ?>
        <span class="absolute top-4 left-4 bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
          Featured
        </span>
      <?php endif; ?>

      <!-- Author Overlay -->
      <?php if (!empty($post['author_name'])): ?>
      <div class="absolute bottom-0 left-0 w-[72%] sm:w-[45%] md:w-[40%] p-4">
        <div class="author-gradient rounded-full flex items-center gap-3 p-3 shadow-md backdrop-blur-sm">
          <img 
            src="<?= htmlspecialchars('./admin/images/authors/' . ($post['author_avatar'] ?: 'default.png')) ?>" 
            alt="Author Avatar" 
            class="w-12 h-12 rounded-full object-cover border border-gray-300"
            onerror="this.src='./admin/images/authors/default.png';"
          >
          <div>
            <p class="text-sm font-semibold text-white"><?= htmlspecialchars($post['author_name']) ?></p>
            <p class="text-xs text-gray-200">Author</p>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Post Header -->
    <div class="mt-10">
      <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
        <?= htmlspecialchars($post['title']) ?>
      </h1>
      <p class="mt-3 text-gray-500 dark:text-gray-400 text-sm">
        Published on <?= date('F j, Y', strtotime($post['created_at'])) ?>
      </p>
    </div>

    <!-- Post Body -->
    <article class="mt-8 text-lg text-gray-700 dark:text-gray-300 leading-relaxed prose dark:prose-invert max-w-none space-y-1">

      <?= nl2br($post['body']) ?>
    </article>

    <!-- Share Section -->
    <div class="mt-12 border-t border-gray-200 dark:border-gray-700 pt-6">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
        Share this post
      </h3>

      <div class="flex flex-wrap gap-3">
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($postUrl) ?>" target="_blank" class="flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-full text-sm font-medium transform hover:scale-105 transition duration-300">
          <i class="fab fa-linkedin"></i> LinkedIn
        </a>

        <a href="https://www.instagram.com/?url=<?= urlencode($postUrl) ?>" target="_blank" class="flex items-center gap-2 bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-full text-sm font-medium transform hover:scale-105 transition duration-300">
          <i class="fab fa-instagram"></i> Instagram
        </a>

        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($postUrl) ?>" target="_blank" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-sm font-medium transform hover:scale-105 transition duration-300">
          <i class="fab fa-facebook-f"></i> Facebook
        </a>

        <a href="https://twitter.com/intent/tweet?url=<?= urlencode($postUrl) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" class="flex items-center gap-2 bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-full text-sm font-medium transform hover:scale-105 transition duration-300">
          <i class="fab fa-x-twitter"></i> Twitter
        </a>

        <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium transform hover:scale-105 transition duration-300">
          <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
      </div>
    </div>

    <!-- Related Posts -->
    <?php if (!empty($relatedPosts)): ?>
      <div class="mt-16 border-t border-gray-200 dark:border-gray-700 pt-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Related Posts</h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($relatedPosts as $related): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg overflow-hidden transition duration-300">
              <a href="post-details.php?id=<?= urlencode($related['id']) ?>" class="block group">
                <img 
                  src="<?= htmlspecialchars('/images/posts/' . $related['thumbnail']) ?>" 
                  alt="<?= htmlspecialchars($related['title']) ?>" 
                  class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                  onerror="this.src='/images/posts/default-post.jpg';"
                >
                <div class="p-4">
                  <h3 class="font-semibold text-gray-800 dark:text-white text-lg group-hover:text-green-600 transition">
                    <?= htmlspecialchars($related['title']) ?>
                  </h3>
                  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <?= date('F j, Y', strtotime($related['created_at'])) ?>
                  </p>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Back Button -->
    <div class="mt-16 text-center">
      <a href="index.php" class="inline-block px-6 py-3 rounded-full border border-gray-400 text-gray-800 dark:text-gray-200 
                hover:bg-green-500 hover:text-black transition font-medium shadow-sm hover:shadow-md">
        ← Back to Homepage
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center py-6 border-t border-gray-200 dark:border-gray-700 mt-10 text-sm text-gray-500 dark:text-gray-400">
    &copy; <?= date('Y') ?> Faharis Industrial and Agrisolutions. All rights reserved.
  </footer>

</body>
</html>
