<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['superuser']);

// Handle license activation/deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action']);
    
    if ($action === 'activate') {
        setSetting('system_licensed', '1');
        $_SESSION['message'] = 'System license activated successfully';
    } elseif ($action === 'deactivate') {
        setSetting('system_licensed', '0');
        $_SESSION['message'] = 'System license deactivated successfully';
    }
    
    header('Location: license.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get license status
$isLicensed = isLicensed();
?>

<h2>License Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="license-status">
    <h3>Current Status: <span style="color: <?= $isLicensed ? 'green' : 'red' ?>">
        <?= $isLicensed ? 'LICENSED' : 'UNLICENSED' ?>
    </span></h3>
    
    <?php if ($isLicensed): ?>
        <form method="POST" action="">
            <input type="hidden" name="action" value="deactivate">
            <button type="submit" class="btn">Deactivate License</button>
        </form>
    <?php else: ?>
        <form method="POST" action="">
            <input type="hidden" name="action" value="activate">
            <button type="submit" class="btn">Activate License</button>
        </form>
    <?php endif; ?>
</div>

<div class="payment-history">
    <h3>License Payment History</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2023-10-10</td>
                <td>Principal</td>
                <td>200,000 XAF</td>
                <td>MTN</td>
                <td>671704285</td>
                <td>Completed</td>
            </tr>
            <tr>
                <td>2023-04-15</td>
                <td>Bourser</td>
                <td>200,000 XAF</td>
                <td>Orange</td>
                <td>657649511</td>
                <td>Completed</td>
            </tr>
        </tbody>
    </table>
</div>

<?php
require_once '../../includes/footer.php';
?>