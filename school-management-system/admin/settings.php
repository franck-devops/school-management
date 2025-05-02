<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['principal']);

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolName = sanitize($_POST['school_name']);
    $schoolAddress = sanitize($_POST['school_address']);
    $schoolPhone = sanitize($_POST['school_phone']);
    $schoolEmail = sanitize($_POST['school_email']);
    $academicYear = sanitize($_POST['academic_year']);
    
    try {
        setSetting('school_name', $schoolName);
        setSetting('school_address', $schoolAddress);
        setSetting('school_phone', $schoolPhone);
        setSetting('school_email', $schoolEmail);
        setSetting('academic_year', $academicYear);
        
        $_SESSION['message'] = 'Settings updated successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error updating settings: ' . $e->getMessage();
    }
    
    header('Location: settings.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get current settings
$schoolName = getSetting('school_name') ?: '';
$schoolAddress = getSetting('school_address') ?: '';
$schoolPhone = getSetting('school_phone') ?: '';
$schoolEmail = getSetting('school_email') ?: '';
$academicYear = getSetting('academic_year') ?: date('Y') . '/' . (date('Y') + 1);
?>

<h2>School Settings</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div>
        <label>School Name:</label>
        <input type="text" name="school_name" value="<?= $schoolName ?>" required>
    </div>
    <div>
        <label>School Address:</label>
        <textarea name="school_address" rows="3"><?= $schoolAddress ?></textarea>
    </div>
    <div>
        <label>School Phone:</label>
        <input type="text" name="school_phone" value="<?= $schoolPhone ?>">
    </div>
    <div>
        <label>School Email:</label>
        <input type="email" name="school_email" value="<?= $schoolEmail ?>">
    </div>
    <div>
        <label>Academic Year:</label>
        <input type="text" name="academic_year" value="<?= $academicYear ?>" required>
    </div>
    <div>
        <button type="submit">Save Settings</button>
    </div>
</form>

<h3>System Information</h3>
<table>
    <tr>
        <th>PHP Version</th>
        <td><?= phpversion() ?></td>
    </tr>
    <tr>
        <th>Database</th>
        <td>MySQL</td>
    </tr>
    <tr>
        <th>Server Software</th>
        <td><?= $_SERVER['SERVER_SOFTWARE'] ?></td>
    </tr>
    <tr>
        <th>Last Backup</th>
        <td><?= getSetting('last_backup') ?: 'Never' ?></td>
    </tr>
</table>

<?php
require_once '../../includes/footer.php';
?>