<?php
require 'config/constants.php';

// Retrieve stored form data if validation failed previously
$firstname = $_SESSION['signup-data']['firstname'] ?? '';
$lastname = $_SESSION['signup-data']['lastname'] ?? '';
$username = $_SESSION['signup-data']['username'] ?? '';
$email = $_SESSION['signup-data']['email'] ?? '';

// Clear the stored form data after displaying
unset($_SESSION['signup-data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup | Fahari Web</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-out {
      animation: fadeOut 0.6s ease-in-out forwards;
    }
    @keyframes fadeOut {
      from { opacity: 1; transform: translateY(0); }
      to { opacity: 0; transform: translateY(-10px); display: none; }
    }
  </style>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center px-4">
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-8 space-y-6 transition-all duration-300">
    <h2 class="text-2xl font-bold text-center text-green-600 dark:text-green-400">Create Your Account</h2>

    <!-- ALERT BOX -->
    <?php if (isset($_SESSION['signup']) || isset($_SESSION['signup-success'])) : ?>
      <?php
        $alert_message = $_SESSION['signup'] ?? $_SESSION['signup-success'];
        $alert_type = isset($_SESSION['signup']) ? 'error' : 'success';
        unset($_SESSION['signup'], $_SESSION['signup-success']);
      ?>
      <div id="alert-box"
           class="relative p-4 rounded-md border 
                  <?= $alert_type === 'error' 
                      ? 'bg-red-100 border-red-400 text-red-700' 
                      : 'bg-green-100 border-green-400 text-green-700' ?>">
        <span><?= $alert_message ?></span>
        <button onclick="closeAlert()"
                class="absolute top-2 right-3 text-lg font-bold text-gray-600 hover:text-gray-900 dark:text-gray-300">
          &times;
        </button>
      </div>
    <?php endif; ?>

    <!-- SIGNUP FORM -->
    <form action="signup-logic.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
          <input type="text" name="firstname" value="<?= htmlspecialchars($firstname) ?>" required
                 class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                        focus:outline-none focus:ring-2 focus:ring-green-500 
                        bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
          <input type="text" name="lastname" value="<?= htmlspecialchars($lastname) ?>" required
                 class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                        focus:outline-none focus:ring-2 focus:ring-green-500 
                        bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required
               class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                      focus:outline-none focus:ring-2 focus:ring-green-500 
                      bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required
               class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                      focus:outline-none focus:ring-2 focus:ring-green-500 
                      bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Create Password</label>
        <input type="password" name="createpassword" required
               class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                      focus:outline-none focus:ring-2 focus:ring-green-500 
                      bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
        <input type="password" name="confirmpassword" required
               class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                      focus:outline-none focus:ring-2 focus:ring-green-500 
                      bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profile Picture</label>
        <input type="file" name="avatar" accept=".png,.jpg,.jpeg"
               class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-md 
                      focus:outline-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
      </div>

      <button type="submit" name="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-md 
                     font-medium transition-colors duration-200">
        Sign Up
      </button>

      <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-4">
        Already have an account?
        <a href="<?= ROOT_URL ?>signin.php" class="text-green-600 dark:text-green-400 font-semibold hover:underline">
          Log In
        </a>
      </p>
    </form>
  </div>

  <script>
    // Close alert manually
    function closeAlert() {
      const alertBox = document.getElementById('alert-box');
      if (alertBox) {
        alertBox.classList.add('fade-out');
        setTimeout(() => alertBox.remove(), 600);
      }
    }

    // Auto-fade after 5 seconds
    window.addEventListener('DOMContentLoaded', () => {
      const alertBox = document.getElementById('alert-box');
      if (alertBox) {
        setTimeout(closeAlert, 5000);
      }
    });
  </script>
</body>
</html>
