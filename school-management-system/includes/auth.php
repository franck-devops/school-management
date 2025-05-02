<?php
require_once 'db.php';
require_once 'functions.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
}

// Check user role
function checkRole($allowedRoles) {
    if (!in_array($_SESSION['user_role'], $allowedRoles)) {
        header('Location: ../login.php?error=unauthorized');
        exit();
    }
}

// Login function
function login($username, $password) {
    global $pdo;
    
    // Special case for superuser
    if ($username === 'sudo-su' && $password === 'sudo-su') {
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = 'sudo-su';
        $_SESSION['user_role'] = 'superuser';
        $_SESSION['full_name'] = 'Super User';
        return true;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        // Update last login
        $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->execute([$user['id']]);
        
        return true;
    }
    
    return false;
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>