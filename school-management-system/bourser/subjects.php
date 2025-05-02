<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['bourser']);

// Handle subject creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $name = sanitize($_POST['name']);
    $code = sanitize($_POST['code']);
    $coefficient = sanitize($_POST['coefficient']);
    $classId = sanitize($_POST['class_id']);
    $teacherId = sanitize($_POST['teacher_id']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO subjects (name, code, coefficient, class_id, teacher_id) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $code, $coefficient, $classId, $teacherId]);
        $_SESSION['message'] = 'Subject added successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error adding subject: ' . $e->getMessage();
    }
    
    header('Location: subjects.php');
    exit();
}

// Handle coefficient update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_coefficient'])) {
    $subjectId = sanitize($_POST['subject_id']);
    $coefficient = sanitize($_POST['new_coefficient']);
    
    try {
        $stmt = $pdo->prepare("UPDATE subjects SET coefficient = ? WHERE id = ?");
        $stmt->execute([$coefficient, $subjectId]);
        $_SESSION['message'] = 'Coefficient updated successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error updating coefficient: ' . $e->getMessage();
    }
    
    header('Location: subjects.php');
    exit();
}

// Handle subject deletion
if (isset($_GET['delete'])) {
    $subjectId = sanitize($_GET['delete']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$subjectId]);
        $_SESSION['message'] = 'Subject deleted successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error deleting subject: ' . $e->getMessage();
    }
    
    header('Location: subjects.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all subjects, classes and staff
$subjects = getSubjects();
$classes = getClasses();
$staff = getStaff();
?>

<h2>Subject Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab('create')">Create Subject</button>
    <button class="tab-btn" onclick="openTab('coefficient')">Update Coefficient</button>
    <button class="tab-btn" onclick="openTab('list')">Subject List</button>
</div>

<div id="create" class="tab-content" style="display: block;">
    <h3>Create New Subject</h3>
    <form method="POST" action="">
        <input type="hidden" name="add_subject" value="1">
        <div>
            <label>Subject Name:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Subject Code:</label>
            <input type="text" name="code" required>
        </div>
        <div>
            <label>Coefficient:</label>
            <input type="number" name="coefficient" min="1" max="10" value="1" required>
        </div>
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
            <label>Teacher:</label>
            <select name="teacher_id">
                <option value="">Select Teacher</option>
                <?php foreach ($staff as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>"><?= $teacher['first_name'] . ' ' . $teacher['last_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit">Create Subject</button>
        </div>
    </form>
</div>

<div id="coefficient" class="tab-content">
    <h3>Update Subject Coefficient</h3>
    <form method="POST" action="">
        <input type="hidden" name="update_coefficient" value="1">
        <div>
            <label>Subject:</label>
            <select name="subject_id" required>
                <option value="">Select Subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?> (<?= $subject['code'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>New Coefficient:</label>
            <input type="number" name="new_coefficient" min="1" max="10" required>
        </div>
        <div>
            <button type="submit">Update Coefficient</button>
        </div>
    </form>
</div>

<div id="list" class="tab-content">
    <h3>Subject List</h3>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Coefficient</th>
                <th>Class</th>
                <th>Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?= $subject['code'] ?></td>
                    <td><?= $subject['name'] ?></td>
                    <td><?= $subject['coefficient'] ?></td>
                    <td><?= $subject['class_id'] ?></td>
                    <td><?= $subject['teacher_id'] ?></td>
                    <td>
                        <a href="#" class="btn">Edit</a>
                        <a href="subjects.php?delete=<?= $subject['id'] ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
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