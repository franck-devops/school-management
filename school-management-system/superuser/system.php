<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['superuser']);

// Handle system setting update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = $_POST['settings'];
    
    foreach ($settings as $key => $value) {
        setSetting($key, $value);
    }
    
    $_SESSION['message'] = 'System settings updated successfully';
    header('Location: system.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get current settings
$systemName = getSetting('system_name') ?: 'School Management System';
$systemEmail = getSetting('system_email') ?: 'admin@school.edu';
$academicYear = getSetting('academic_year') ?: date('Y') . '/' . (date('Y') + 1);
?>

<h2>System Settings</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div>
        <label>System Name:</label>
        <input type="text" name="settings[system_name]" value="<?= $systemName ?>" required>
    </div>
    <div>
        <label>System Email:</label>
        <input type="email" name="settings[system_email]" value="<?= $systemEmail ?>" required>
    </div>
    <div>
        <label>Academic Year:</label>
        <input type="text" name="settings[academic_year]" value="<?= $academicYear ?>" required>
    </div>
    <div>
        <label>Maintenance Mode:</label>
        <select name="settings[maintenance_mode]">
            <option value="0" <?= getSetting('maintenance_mode') == '0' ? 'selected' : '' ?>>Disabled</option>
            <option value="1" <?= getSetting('maintenance_mode') == '1' ? 'selected' : '' ?>>Enabled</option>
        </select>
    </div>
    <div>
        <button type="submit">Save Settings</button>
    </div>
</form>

<h3>User Management</h3>
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Last Login</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach (getAllUsers() as $user): ?>
            <tr>
                <td><?= $user['username'] ?></td>
                <td><?= $user['full_name'] ?></td>
                <td><?= getRoleName($user['role']) ?></td>
                <td><?= $user['last_login'] ?: 'Never' ?></td>
                <td><?= ucfirst($user['status']) ?></td>
                <td>
                    <a href="#" class="btn">Edit</a>
                    <a href="#" class="btn"><?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once '../../includes/footer.php';
?>