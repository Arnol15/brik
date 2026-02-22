<?php
require_once __DIR__ . '/config/database.php';
include 'partials/navbar.php';
?>
<!-- Tailwind CSS -->
 <link rel="stylesheet" href="./css/style.css">


<script src="https://cdn.tailwindcss.com"></script>
<section class="bg-gray-50 dark:bg-gray-900 min-h-screen pt-24">

  <!-- 🏗 Projects Section -->
  <div class="max-w-7xl mx-auto px-4 mb-16">
    <h2 class="text-3xl font-bold text-center text-[#014d3a] mb-8">Our Project Success Stories</h2>

    <div class="grid md:grid-cols-3 gap-8">
      <?php
      $projects = mysqli_query($connection, "SELECT * FROM projects ORDER BY created_at DESC LIMIT 6");
      if ($projects && mysqli_num_rows($projects) > 0):
          while ($project = mysqli_fetch_assoc($projects)):
              // Determine image path
              $projectImage = !empty($project['image']) 
                ? './images/projects/' . htmlspecialchars($project['image']) 
                : './images/projects/default-project.jpg';
      ?>
        <div class="bg-white dark:bg-[#1e2d25] rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
          <img src="<?= $projectImage ?>" 
               alt="<?= htmlspecialchars($project['title']) ?>" 
               class="w-full h-52 object-cover"
               onerror="this.src='./images/projects/default-project.jpg';">
          <div class="p-6">
            <h3 class="text-xl font-semibold text-[#014d3a] mb-2">
              <?= htmlspecialchars($project['title']) ?>
            </h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 leading-relaxed">
              <?= htmlspecialchars(substr(strip_tags($project['description'] ?? ''), 0, 150)) ?>...
            </p>

            <a href="project-details.php?id=<?= urlencode($project['id']) ?>"
               class="inline-flex items-center text-white bg-[#014d3a] hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium transition">
              View Details
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
              </svg>
            </a>
          </div>
        </div>
      <?php
          endwhile;
      else:
          echo '<p class="col-span-3 text-center text-gray-500 dark:text-gray-400">No projects found at the moment.</p>';
      endif;
      ?>
    </div>

    <div class="flex justify-center mt-10">
      <a href="./projects-all.php" class="bg-[#014d3a] hover:bg-green-700 text-white px-6 py-2 rounded-md shadow-md">
        View All Projects
      </a>
    </div>
  </div>

  <!-- 📰 Posts Section -->
  <div class="bg-[#014d3a]/10 py-12">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-3xl font-bold text-center text-[#014d3a] mb-8">Latest Industry Insights</h2>

      <div class="grid md:grid-cols-2 gap-10">
        <?php
        $posts = mysqli_query($connection, "SELECT * FROM posts ORDER BY created_at DESC LIMIT 4");
        if ($posts && mysqli_num_rows($posts) > 0):
            while ($post = mysqli_fetch_assoc($posts)):
                // Determine image path
                $postImage = !empty($post['thumbnail']) 
                  ? './images/posts/' . htmlspecialchars($post['thumbnail']) 
                  : (!empty($post['image']) 
                      ? './images/posts/' . htmlspecialchars($post['image']) 
                      : './images/posts/default-post.jpg');
        ?>
          <div class="flex flex-col md:flex-row bg-white dark:bg-[#1e2d25] rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300">
            <img src="<?= $postImage ?>" 
                 alt="<?= htmlspecialchars($post['title']) ?>" 
                 class="w-full md:w-1/3 h-52 object-cover"
                 onerror="this.src='./images/posts/default-post.jpg';">
            <div class="p-6 flex flex-col justify-between">
              <div>
                <h3 class="text-xl font-semibold text-[#014d3a] mb-2">
                  <?= htmlspecialchars($post['title']) ?>
                </h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 leading-relaxed">
                  <?= htmlspecialchars(substr(strip_tags($post['body'] ?? $post['description'] ?? ''), 0, 160)) ?>...
                </p>
              </div>

              <a href="post-details.php?id=<?= urlencode($post['id']) ?>" 
                 class="inline-block mt-3 text-[#014d3a] hover:text-green-700 font-medium">
                Read More →
              </a>
            </div>
          </div>
        <?php
            endwhile;
        else:
            echo '<p class="col-span-2 text-center text-gray-500 dark:text-gray-400">No posts available at the moment.</p>';
        endif;
        ?>
      </div>

      <div class="flex justify-center mt-10">
        <a href="./posts-all.php" class="bg-[#014d3a] hover:bg-green-700 text-white px-6 py-2 rounded-md shadow-md">
          View All Posts
        </a>
      </div>
    </div>
  </div>

</section>

<?php include 'partials/footer.php'; ?>
