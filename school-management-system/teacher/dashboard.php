<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['teacher']);
?>

<h2>Teacher Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <h3>My Subjects</h3>
        <p>5</p>
    </div>
    <div class="stat-card">
        <h3>Classes</h3>
        <p>3</p>
    </div>
    <div class="stat-card">
        <h3>Students</h3>
        <p>120</p>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>