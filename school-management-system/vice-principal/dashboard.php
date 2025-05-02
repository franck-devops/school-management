<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['vice_principal']);
?>

<h2>Vice Principal Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <h3>Total Staff</h3>
        <p><?= count(getStaff()) ?></p>
    </div>
    <div class="stat-card">
        <h3>Teachers</h3>
        <p><?= count(array_filter(getStaff(), function($s) { return $s['role'] === 'teacher'; })) ?></p>
    </div>
    <div class="stat-card">
        <h3>HODs</h3>
        <p><?= count(array_filter(getStaff(), function($s) { return $s['role'] === 'hod'; })) ?></p>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>