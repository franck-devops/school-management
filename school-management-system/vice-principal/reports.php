<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['vice_principal']);

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    $classId = sanitize($_POST['class_id']);
    $term = sanitize($_POST['term']);
    
    // In a real implementation, this would generate a PDF report
    // For now, we'll just simulate it
    $_SESSION['message'] = "Report generated for class ID $classId, $term";
    
    header('Location: reports.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all classes
$classes = getClasses();
?>

<h2>Report Card Generation</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="generate_report" value="1">
    <div>
        <label>Class:</label>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?= $class['id'] ?>"><?= $class['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Term:</label>
        <select name="term" required>
            <option value="term1">Term 1 (Sequence 1 & 2)</option>
            <option value="term2">Term 2 (Sequence 3 & 4)</option>
            <option value="term3">Term 3 (Sequence 5 & 6)</option>
        </select>
    </div>
    <div>
        <button type="submit">Generate Report Cards (PDF)</button>
    </div>
</form>

<?php
require_once '../../includes/footer.php';
?>