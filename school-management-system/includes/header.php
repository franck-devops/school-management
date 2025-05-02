<?php
require_once 'auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1>School Management System</h1>
            <nav>
                <ul>
                    <li><a href="<?= BASE_URL . $_SESSION['user_role'] ?>/dashboard.php">Dashboard</a></li>
                    <?php if ($_SESSION['user_role'] === 'principal'): ?>
                        <li><a href="<?= BASE_URL ?>admin/analytics.php">Analytics</a></li>
                        <li><a href="<?= BASE_URL ?>admin/license.php">License</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'bourser'): ?>
                        <li><a href="<?= BASE_URL ?>bourser/students.php">Students</a></li>
                        <li><a href="<?= BASE_URL ?>bourser/classes.php">Classes</a></li>
                        <li><a href="<?= BASE_URL ?>bourser/subjects.php">Subjects</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'vice_principal'): ?>
                        <li><a href="<?= BASE_URL ?>vice-principal/staff.php">Staff</a></li>
                        <li><a href="<?= BASE_URL ?>vice-principal/reports.php">Reports</a></li>
                        <li><a href="<?= BASE_URL ?>vice-principal/promotions.php">Promotions</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'teacher'): ?>
                        <li><a href="<?= BASE_URL ?>teacher/marks.php">Marks</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'superuser'): ?>
                        <li><a href="<?= BASE_URL ?>superuser/system.php">System</a></li>
                        <li><a href="<?= BASE_URL ?>superuser/license.php">License</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>logout.php">Logout</a></li>
                </ul>
            </nav>
            <div class="user-info">
                Welcome, <?= $_SESSION['full_name'] ?> (<?= getRoleName($_SESSION['user_role']) ?>)
            </div>
        </div>
    </header>
    <main class="container">