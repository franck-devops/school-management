<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['bourser']);

// Handle class creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $className = sanitize($_POST['name']);
    $section = sanitize($_POST['section']);
    $classTeacherId = sanitize($_POST['class_teacher_id']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO classes (name, section, class_teacher_id) VALUES (?, ?, ?)");
        $stmt->execute([$className, $section, $classTeacherId]);
        $_SESSION['message'] = 'Class added successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error adding class: ' . $e->getMessage();
    }
    
    header('Location: classes.php');
    exit();
}

// Handle import from Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_classes'])) {
    $result = importFromExcel($_FILES['class_file'], 'classes');
    $_SESSION['message'] = $result['success'] ? 
        'Successfully imported ' . $result['imported'] . ' classes' : 
        'Error importing classes';
    
    header('Location: classes.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all classes and staff
$classes = getClasses();
$staff = getStaff();
?>

<h2>Class Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab('create')">Create Class</button>
    <button class="tab-btn" onclick="openTab('import')">Import from Excel</button>
    <button class="tab-btn" onclick="openTab('list')">Class List</button>
</div>

<div id="create" class="tab-content" style="display: block;">
    <h3>Create New Class</h3>
    <form method="POST" action="">
        <input type="hidden" name="add_class" value="1">
        <div>
            <label>Class Name:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Section:</label>
            <select name="section" required>
                <option value="general">General</option>
                <option value="commercial">Commercial</option>
                <option value="technical">Technical</option>
            </select>
        </div>
        <div>
            <label>Class Teacher:</label>
            <select name="class_teacher_id">
                <option value="">Select Teacher</option>
                <?php foreach ($staff as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>"><?= $teacher['first_name'] . ' ' . $teacher['last_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit">Create Class</button>
        </div>
    </form>
</div>

<div id="import" class="tab-content">
    <h3>Import Classes from Excel</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="import_classes" value="1">
        <div>
            <label>Excel File:</label>
            <input type="file" name="class_file" accept=".xlsx,.xls" required>
        </div>
        <div>
            <button type="submit">Import Classes</button>
        </div>
    </form>
</div>

<div id="list" class="tab-content">
    <h3>Class List</h3>
    <table>
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Section</th>
                <th>Class Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $class): ?>
                <tr>
                    <td><?= $class['name'] ?></td>
                    <td><?= ucfirst($class['section']) ?></td>
                    <td><?= $class['class_teacher_id'] ?></td>
                    <td>
                        <a href="#" class="btn">Edit</a>
                        <a href="#" class="btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function openTab(tabName) {
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = 'none';
    }
    
    const tabButtons = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }
    
    document.getElementById(tabName).style.display = 'block';
    event.currentTarget.classList.add('active');
}
</script>

<?php
require_once '../../includes/footer.php';
?>