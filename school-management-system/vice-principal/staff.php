<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['vice_principal']);

// Handle staff registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $staffId = sanitize($_POST['staff_id']);
    $firstName = sanitize($_POST['first_name']);
    $lastName = sanitize($_POST['last_name']);
    $gender = sanitize($_POST['gender']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $role = sanitize($_POST['role']);
    $subjectId = sanitize($_POST['subject_id']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO staff (staff_id, first_name, last_name, gender, email, phone, role, subject_id) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$staffId, $firstName, $lastName, $gender, $email, $phone, $role, $subjectId]);
        $_SESSION['message'] = 'Staff member added successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error adding staff member: ' . $e->getMessage();
    }
    
    header('Location: staff.php');
    exit();
}

// Handle import from Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_staff'])) {
    $result = importFromExcel($_FILES['staff_file'], 'staff');
    $_SESSION['message'] = $result['success'] ? 
        'Successfully imported ' . $result['imported'] . ' staff members' : 
        'Error importing staff';
    
    header('Location: staff.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all staff and subjects
$staff = getStaff();
$subjects = getSubjects();
?>

<h2>Staff Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab('register')">Register Staff</button>
    <button class="tab-btn" onclick="openTab('import')">Import from Excel</button>
    <button class="tab-btn" onclick="openTab('list')">Staff List</button>
</div>

<div id="register" class="tab-content" style="display: block;">
    <h3>Register New Staff Member</h3>
    <form method="POST" action="">
        <input type="hidden" name="add_staff" value="1">
        <div>
            <label>Staff ID:</label>
            <input type="text" name="staff_id" required>
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
            <label>Email:</label>
            <input type="email" name="email">
        </div>
        <div>
            <label>Phone:</label>
            <input type="text" name="phone">
        </div>
        <div>
            <label>Role:</label>
            <select name="role" required>
                <option value="teacher">Teacher</option>
                <option value="hod">HOD</option>
                <option value="class_master">Class Master</option>
                <option value="both">Both (HOD & Class Master)</option>
            </select>
        </div>
        <div>
            <label>Subject:</label>
            <select name="subject_id">
                <option value="">Select Subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit">Register Staff</button>
        </div>
    </form>
</div>

<div id="import" class="tab-content">
    <h3>Import Staff from Excel</h3>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="import_staff" value="1">
        <div>
            <label>Excel File:</label>
            <input type="file" name="staff_file" accept=".xlsx,.xls" required>
        </div>
        <div>
            <button type="submit">Import Staff</button>
        </div>
    </form>
</div>

<div id="list" class="tab-content">
    <h3>Staff List</h3>
    <table>
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Role</th>
                <th>Subject</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staff as $member): ?>
                <tr>
                    <td><?= $member['staff_id'] ?></td>
                    <td><?= $member['first_name'] . ' ' . $member['last_name'] ?></td>
                    <td><?= ucfirst($member['gender']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $member['role'])) ?></td>
                    <td><?= $member['subject_id'] ?></td>
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