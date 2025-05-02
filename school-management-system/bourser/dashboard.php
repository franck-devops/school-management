<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['bourser']);
?>

<h2>Bourser Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <h3>Total Students</h3>
        <p><?= count(getStudents()) ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Classes</h3>
        <p><?= count(getClasses()) ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Subjects</h3>
        <p><?= count(getSubjects()) ?></p>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>