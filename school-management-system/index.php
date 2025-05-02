<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Check license status
if (!isLicensed() && !(isset($_GET['login']) && $_GET['login'] === 'sudo-su')) {
    die('<h1>License Required</h1><p>The system is currently unlicensed. Please contact the administrator.</p>');
}

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Redirect to appropriate dashboard
header('Location: ' . $_SESSION['user_role'] . '/dashboard.php');
exit();
?>