<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['superuser']);
?>

<h2>Superuser Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <h3>Total Users</h3>
        <p><?= count(getAllUsers()) ?></p>
    </div>
    <div class="stat-card">
        <h3>System Status</h3>
        <p><?= isLicensed() ? 'Licensed' : 'Unlicensed' ?></p>
    </div>
    <div class="stat-card">
        <h3>Last Backup</h3>
        <p><?= getSetting('last_backup') ?: 'Never' ?></p>
    </div>
</div>

<div class="quick-actions">
    <h3>Quick Actions</h3>
    <div>
        <a href="system.php" class="btn">System Settings</a>
        <a href="license.php" class="btn">License Management</a>
        <a href="#" class="btn" onclick="backupSystem()">Backup System</a>
    </div>
</div>

<script>
function backupSystem() {
    if (confirm('Are you sure you want to create a system backup?')) {
        $.post('../../includes/backup.php', function(response) {
            alert(response.message);
            if (response.success) {
                location.reload();
            }
        }, 'json');
    }
}
</script>

<?php
require_once '../../includes/footer.php';
?>