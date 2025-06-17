<?php
// includes/header.php
// Ensure session is started (might be done in auth.php already)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Alpine only on pages that need it (index.php) -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php endif; ?>
    <title><?php echo $pageTitle ?? 'Todo App'; // Allow setting page title ?></title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold">Todoist Clone</a>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                    <a href="logout.php" class="hover:underline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="hover:underline mr-4">Login</a>
                    <a href="register.php" class="hover:underline">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Main content starts after this in each page -->
