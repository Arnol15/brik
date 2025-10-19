<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Keep form data if validation fails
$title = $_SESSION['add-post-data']['title'] ?? '';
$body = $_SESSION['add-post-data']['body'] ?? '';
unset($_SESSION['add-post-data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Post</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <!--  TinyMCE -->
  <script src="<?= ROOT_URL ?>js/tinymce/tinymce.min.js"></script>
  <script>
    tinymce.init({
      selector: '#body',
      height: 400,
      menubar: false,
      plugins: 'link image code lists table media',
      toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | code',
      skin: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide',
      content_css: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default',

      /* Allow image upload */
      images_upload_url: '../logic/upload-image.php',  // must exist
      automatic_uploads: true,
      file_picker_types: 'image',
      file_picker_callback: function (callback, value, meta) {
        if (meta.filetype === 'image') {
          const input = document.createElement('input');
          input.setAttribute('type', 'file');
          input.setAttribute('accept', 'image/*');
          input.onchange = function () {
            const file = this.files[0];
            const reader = new FileReader();
            reader.onload = function () {
              const id = 'blobid' + (new Date()).getTime();
              const blobCache = tinymce.activeEditor.editorUpload.blobCache;
              const base64 = reader.result.split(',')[1];
              const blobInfo = blobCache.create(id, file, base64);
              blobCache.add(blobInfo);
              callback(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
          };
          input.click();
        }
      },
      promotion: false,
      license_key: 'gpl'
    });
  </script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

  <div class="bg-white shadow-md rounded-xl p-6 sm:p-8 w-full max-w-3xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Add New Post</h2>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['add-post'])): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-600 rounded">
        <?= $_SESSION['add-post']; unset($_SESSION['add-post']); ?>
      </div>
    <?php elseif (isset($_SESSION['add-post-success'])): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        <?= $_SESSION['add-post-success']; unset($_SESSION['add-post-success']); ?>
      </div>
    <?php endif; ?>

    <!-- Form -->
    <form id="addPostForm" action="logic/add-post-logic.php" method="POST" enctype="multipart/form-data" class="space-y-6">


      <!-- Title -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Post Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required
          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-green-600">
      </div>

      <!-- Category -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
        <select name="category" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-green-600">
          <option value="">-- Select Category --</option>
          <?php
            $categories = mysqli_query($connection, "SELECT * FROM categories");
            while ($cat = mysqli_fetch_assoc($categories)) :
          ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Body -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Post Content</label>
        <textarea id="body" name="body" rows="10" required class="w-full border rounded-md p-3"><?= htmlspecialchars($body) ?></textarea>
      </div>

      <!-- Thumbnail Upload -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail</label>
        <input type="file" name="thumbnail" accept="image/*" id="thumbnail" required
          class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 
          file:bg-green-700 file:text-white hover:file:bg-green-800 cursor-pointer"
          onchange="previewImage(event)">
        
        <!-- Preview -->
        <div id="preview-container" class="hidden mt-3">
          <p class="text-sm text-gray-600 mb-2">Preview:</p>
          <img id="preview" src="#" alt="Thumbnail Preview" class="w-32 h-32 object-cover rounded-md border">
        </div>
      </div>

      <!-- Featured -->
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_featured" value="1" id="is_featured" class="h-4 w-4 text-green-600">
        <label for="is_featured" class="text-sm text-gray-700">Feature this post</label>
      </div>

      <!-- Submit -->
      <button type="submit" name="submit" class="w-full py-3 bg-green-700 text-white font-medium rounded-md hover:bg-green-800 transition">
        Publish Post
      </button>
    </form>
  </div>

  <!-- Image Preview Script -->
  <script>
    function previewImage(event) {
      const preview = document.getElementById('preview');
      const container = document.getElementById('preview-container');
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          container.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
      } else {
        container.classList.add('hidden');
        preview.src = '#';
      }
    }
  </script>

</body>
</html>
