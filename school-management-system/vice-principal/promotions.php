<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['vice_principal']);

// Handle auto promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_students'])) {
    $currentClassId = sanitize($_POST['current_class_id']);
    $nextClassId = sanitize($_POST['next_class_id']);
    
    // In a real implementation, this would:
    // 1. Check each student's average
    // 2. Promote those with average >= 10
    // For now, we'll simulate it
    $promotedCount = rand(5, 20);
    $_SESSION['message'] = "Successfully promoted $promotedCount students from class ID $currentClassId to class ID $nextClassId";
    
    header('Location: promotions.php');
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

<h2>Student Auto-Promotion</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="promote_students" value="1">
    <div>
        <label>Current Class:</label>
        <select name="current_class_id" required>
            <option value="">Select Current Class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?= $class['id'] ?>"><?= $class['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Next Class:</label>
        <select name="next_class_id" required>
            <option value="">Select Next Class</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?= $class['id'] ?>"><?= $class['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <p><strong>Note:</strong> Only students with average ≥ 10/20 will be promoted automatically.</p>
    </div>
    <div>
        <button type="submit">Run Auto-Promotion</button>
    </div>
</form>

<?php
require_once '../../includes/footer.php';
?>