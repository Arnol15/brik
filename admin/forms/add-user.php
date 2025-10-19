<?php

// fetch stored form data if validation failed previously
$firstname = $_SESSION['add-user-data']['firstname'] ?? '';
$lastname = $_SESSION['add-user-data']['lastname'] ?? '';
$username = $_SESSION['add-user-data']['username'] ?? '';
$email = $_SESSION['add-user-data']['email'] ?? '';
unset($_SESSION['add-user-data']);
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a2e0e9b6b2.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 dark:bg-[#1b2a2f] flex items-center justify-center min-h-screen transition-colors duration-300">

  <div class="bg-white dark:bg-[#2a3b45] shadow-lg rounded-2xl w-full max-w-5xl p-8 mx-4 md:mx-auto flex flex-col md:flex-row gap-10 transition-colors duration-300">
    
    <!-- Avatar Upload + Preview Section -->
    <div class="md:w-1/3 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700 pb-6 md:pb-0">
      <div class="relative w-48 h-48 mb-4">
        <img id="avatarPreview" 
             src="https://via.placeholder.com/150?text=Preview" 
             alt="Avatar Preview" 
             class="w-full h-full object-cover rounded-full border-4 border-gray-300 dark:border-gray-600 shadow">
      </div>

      <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Upload Avatar</label>
      <input type="file" 
             name="avatar" 
             id="avatarInput" 
             accept=".png,.jpg,.jpeg" 
             required 
             form="addUserForm"
             class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-sm bg-gray-50 dark:bg-[#1f2d33] dark:text-gray-200 cursor-pointer focus:ring focus:ring-green-300">
    </div>

    <!-- Form Fields Section -->
    <div class="md:w-2/3">
      <h2 class="text-2xl font-semibold mb-6 text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-600 pb-2">Add New User</h2>

      <?php if (isset($_SESSION['add-user'])): ?>
        <div class="bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 px-4 py-2 rounded mb-4">
          <?= $_SESSION['add-user']; unset($_SESSION['add-user']); ?>
        </div>
      <?php elseif (isset($_SESSION['add-user-success'])): ?>
        <div class="bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 px-4 py-2 rounded mb-4">
          <?= $_SESSION['add-user-success']; unset($_SESSION['add-user-success']); ?>
        </div>
      <?php endif; ?>

      <form id="addUserForm" action="logic/add-user-logic.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">First Name</label>
            <input type="text" name="firstname" value="<?= htmlspecialchars($firstname) ?>" required 
              class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
          </div>

          <div>
            <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Last Name</label>
            <input type="text" name="lastname" value="<?= htmlspecialchars($lastname) ?>" required 
              class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
          </div>

          <div>
            <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required 
              class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
          </div>

          <div>
            <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required 
              class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Create Password</label>
            <input type="password" name="createpassword" required 
              class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
          </div>

          <div>
            <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Confirm Password</label>
            <input type="password" name="confirmpassword" required 
              class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-white dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
          </div>
        </div>

        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">User Role</label>
          <select name="userrole" 
            class="w-full border border-gray-300 dark:border-gray-600 rounded p-2 bg-gray-50 dark:bg-[#1f2d33] text-gray-800 dark:text-gray-100 focus:ring focus:ring-green-300">
            <option value="0">Normal User</option>
            <option value="1">Admin</option>
          </select>
        </div>

        <button type="submit" name="submit" 
          class="w-full bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg font-medium transition duration-200 ease-in-out shadow-md hover:shadow-lg">
          <i class="fa fa-user-plus mr-2"></i> Add User
        </button>
      </form>
    </div>
  </div>

  <!--  JavaScript for Avatar Preview -->
  <script>
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    avatarInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          avatarPreview.src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  </script>

</body>
</html>

