<?php
require 'config/database.php';

// ✅ Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = null;
$loading = false;

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loading = true;

    $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"] ?? "");

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // ✅ Query the user record
        $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);

                // ✅ Verify password
                if (password_verify($password, $user['password'])) {
                    // ✅ Successful login
                    $_SESSION['user-id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_avatar'] = $user['avatar'] ?? 'default.png';
                    $_SESSION['last_activity'] = time(); // track activity for auto logout

                    if (!empty($user['is_admin']) && $user['is_admin'] == 1) {
                        $_SESSION['user_is_admin'] = true;
                    }

                    header("Location: admin/index.php");
                    exit;
                } else {
                    $error = "Incorrect password. Please try again.";
                }
            } else {
                $error = "No account found with that email address.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = "Database query failed. Please try again later.";
        }
    }

    $loading = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    input:focus {
      outline: none !important;
      box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.45);
      border-color: #22d3ee;
    }
    body {
      transition: background-color 0.3s, color 0.3s;
    }
    .btn-green {
      transition: all 0.25s ease;
    }
    .btn-green:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
    }
    .link-soft {
      transition: color 0.2s ease;
    }
    .link-soft:hover {
      color: #0d9488;
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 py-12 px-4 relative">

  <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>"
    class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl 
           w-full sm:max-w-md md:max-w-lg lg:max-w-xl 
           p-6 sm:p-8 lg:p-10 flex flex-col items-center transition-colors duration-300">

    <!-- Avatar -->
    <div class="w-20 h-20 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-6">
      <img src="./images/user.svg" alt="avatar" class="w-10 h-10">
    </div>

    <!-- Title -->
    <h2 class="text-2xl font-semibold mb-8 text-gray-800 dark:text-white">Welcome Back</h2>

    <!-- ✅ Session Expired Message -->
    <?php if (isset($_GET['session']) && $_GET['session'] === 'expired'): ?>
      <div class="w-full mb-4 text-sm text-yellow-700 dark:text-yellow-400 text-center font-medium bg-yellow-100 dark:bg-yellow-800/40 rounded-md p-3">
        Your session has expired due to inactivity. Please sign in again.
      </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (!empty($error)): ?>
      <div class="w-full mb-4 text-sm text-red-600 dark:text-red-400 text-center font-medium bg-red-100 dark:bg-red-800/40 rounded-md p-3">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Email -->
    <div class="w-full mb-4">
      <label for="email" class="block text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
      <input type="email" id="email" name="email" required
        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-md 
               dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-cyan-400 transition">
    </div>

    <!-- Password -->
    <div class="w-full mb-6">
      <label for="password" class="block text-gray-700 dark:text-gray-300 mb-1">Password</label>
      <input type="password" id="password" name="password" required
        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-md 
               dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-cyan-400 transition">
    </div>

    <!-- Submit -->
    <button type="submit"
      class="btn-green w-full bg-green-600 text-white py-3 rounded-md font-medium 
             hover:bg-green-700 transition disabled:opacity-60 mb-6">
      <?= $loading ? "Signing in…" : "Sign In" ?>
    </button>

    <!-- Optional: Social Auth (future use) -->
    <div class="flex flex-col gap-2 w-full mb-6">
      <button type="button" class="w-full flex items-center justify-center gap-2 py-3 border rounded-md 
                                  dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <img src="./images/google.svg" alt="Google" class="w-5 h-5"> Sign in with Google
      </button>
      <button type="button" class="w-full flex items-center justify-center gap-2 py-3 border rounded-md 
                                  dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <img src="./images/github.svg" alt="GitHub" class="w-5 h-5"> Sign in with GitHub
      </button>
    </div>

    <!-- Links -->
    <div class="w-full text-sm text-center text-teal-600 dark:text-teal-400 flex flex-col gap-1">
      <a href="signup.php" class="hover:underline link-soft">Don’t have an account? Sign up</a>
      <a href="/forgot-password.php" class="hover:underline link-soft">Forgot password?</a>
    </div>
  </form>

  <div class="absolute bottom-4 text-xs text-gray-500 dark:text-gray-400">
    Privacy Policy © <?= date('Y') ?> Fahari Systems
  </div>

</body>
</html>

