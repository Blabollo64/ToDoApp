<?php
function logoutUser() {
    if (!isset($_SESSION)) {
        session_start();
    }
    // Unset all session variables
    $_SESSION = [];
    // Destroy the session
    session_destroy();
}

function isLoggedIn() {
    if (!isset($_SESSION)) {
        session_start();
    }
    return !empty($_SESSION['user_id']);
}

 function loginUser($username, $password) {
    // Replace with your actual DB connection logic
    $pdo = connectDB();

    // Fetch user by username
    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : false;

    if ($user && password_verify($password, $user['password'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    return false;
}

    function requireLogin() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUserId() {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}
?>