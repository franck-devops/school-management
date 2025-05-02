<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['bourser']);

// Handle student registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $admissionNumber = sanitize($_POST['admission_number']);
    $firstName = sanitize($_POST['first_name']);
    $lastName = sanitize($_POST['last_name']);
    $gender = sanitize($_POST['gender']);
    $dateOfBirth = sanitize($_POST['date_of_birth']);
    $classId = sanitize($_POST['class_id']);
    $section = sanitize($_POST['section']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO students (admission_number, first_name, last_name, gender, date_of_birth, class_id, section) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$admissionNumber, $firstName, $lastName, $gender, $dateOfBirth, $classId, $section]);
        $_SESSION['message'] = 'Student added successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error adding student: ' . $e->getMessage();
    }
    
    header('Location: students.php');
    exit();
}

// Handle import from Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_students'])) {
    $result = importFromExcel($_FILES['student_file'], 'students');
    $_SESSION['message'] = $result['success'] ? 
        'Successfully imported ' . $result['imported'] . ' students' : 
        'Error importing students';
    
    header('Location: students.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all students
$students = getStudents();
$classes = getClasses();
?>

<h2>Student Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab('register')">Register Student</button>
    <button class="tab-btn" onclick="openTab('import')">Import from Excel</button>
    <button class="tab-btn" onclick="openTab('list')">Student List</button>
</div>

<div id="register" class="tab-content" style="display: block;">
    <h3>Register New Student</h3>
    <form method="POST" action="">
        <input type="hidden" name="add_student" value="1">
        <div>
            <label>Admission Number:</label>
            <input type="text" name="admission_number" required>
        </div>
        <div>
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>
        <div>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>
        <div>
            <label>Gender:</label>
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div>
            <label>Date of Birth:</label>
            <input type="date" name="date_of_birth">
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
            <label>Section:</label>
            <select name="section" required>
                <option value="general">General</option>
                <option value="commercial">Commercial</option>
                <option value="technical">Technical</option>
            </select>
        </div>
        <div>
            <button type="submit">Register Student</button>
        </div>
    </form>
</div>

<div id="import" class="tab-content">
    <h3>Import Students from Excel</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="import_students" value="1">
        <div>
            <label>Excel File:</label>
            <input type="file" name="student_file" accept=".xlsx,.xls" required>
        </div>
        <div>
            <button type="submit">Import Students</button>
        </div>
    </form>
</div>

<div id="list" class="tab-content">
    <h3>Student List</h3>
    <table>
        <thead>
            <tr>
                <th>Admission No.</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Class</th>
                <th>Section</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['admission_number'] ?></td>
                    <td><?= $student['first_name'] . ' ' . $student['last_name'] ?></td>
                    <td><?= ucfirst($student['gender']) ?></td>
                    <td><?= $student['class_id'] ?></td>
                    <td><?= ucfirst($student['section']) ?></td>
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