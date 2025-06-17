
<?php
session_start(); // Start the session to access session variables
// public/register.php
require_once __DIR__ . '/../includes/auth.php';

$error = '';
$success = '';

// Redirect if logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username, password, maybe confirm_password
    // Validate input (e.g., password match, length)
    // If valid, call registerUser()
    // Set $success or $error message
    /* ... Placeholder for registration form handling ... */
    $error = "Registration logic not implemented."; // Placeholder
}

$pageTitle = "Register";
include __DIR__ . '/../includes/header.php';
?>
<!-- HTML Form similar to login, with fields for username, password, confirm password -->
<!-- Display $error or $success messages -->
<div class="container mx-auto max-w-sm mt-10">
    <h2 class="text-2xl font-bold mb-4 text-center">Register</h2>
    <?php if ($error): ?>
        <p class="text-red-500 text-center mb-2"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="text-green-500 text-center mb-2"><?php echo $success; ?></p>
    <?php endif; ?>
    <form action="register.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   id="username" name="username" type="text" required autofocus>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   id="password" name="password" type="password" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">Confirm Password</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   id="confirm_password" name="confirm_password" type="password" required>
        </div>
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                Register
            </button>
        </div>
    </form>
    <p class="text-center mt-4">
        Already have an account?
        <a href="login.php" class="text-blue-500 hover:underline">Login here</a>
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
