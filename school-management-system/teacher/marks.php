<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['teacher']);

// Handle marks submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_marks'])) {
    $studentId = sanitize($_POST['student_id']);
    $subjectId = sanitize($_POST['subject_id']);
    $term = sanitize($_POST['term']);
    $sequence = sanitize($_POST['sequence']);
    $mark = sanitize($_POST['mark']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO marks (student_id, subject_id, term, sequence, mark, created_by) 
                              VALUES (?, ?, ?, ?, ?, ?) 
                              ON DUPLICATE KEY UPDATE mark = ?, updated_at = NOW()");
        $stmt->execute([$studentId, $subjectId, $term, $sequence, $mark, $_SESSION['user_id'], $mark]);
        $_SESSION['message'] = 'Marks submitted successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error submitting marks: ' . $e->getMessage();
    }
    
    header('Location: marks.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all students, subjects (that the teacher teaches)
$students = getStudents();
$subjects = array_filter(getSubjects(), function($s) { return $s['teacher_id'] == $_SESSION['user_id']; });
?>

<h2>Marks Registration</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="submit_marks" value="1">
    <div>
        <label>Student:</label>
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['id'] ?>"><?= $student['first_name'] . ' ' . $student['last_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Subject:</label>
        <select name="subject_id" required>
            <option value="">Select Subject</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
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
        <label>Sequence:</label>
        <select name="sequence" required>
            <option value="sequence1">Sequence 1</option>
            <option value="sequence2">Sequence 2</option>
            <option value="sequence3">Sequence 3</option>
            <option value="sequence4">Sequence 4</option>
            <option value="sequence5">Sequence 5</option>
            <option value="sequence6">Sequence 6</option>
        </select>
    </div>
    <div>
        <label>Mark (out of 20):</label>
        <input type="number" name="mark" min="0" max="20" step="0.25" required>
    </div>
    <div>
        <button type="submit">Submit Marks</button>
    </div>
</form>

<h3>Recent Marks</h3>
<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Subject</th>
            <th>Term</th>
            <th>Sequence</th>
            <th>Mark</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>John Doe</td>
            <td>Mathematics</td>
            <td>Term 1</td>
            <td>Sequence 1</td>
            <td>15.5</td>
            <td>2023-10-15</td>
        </tr>
        <tr>
            <td>Jane Smith</td>
            <td>Physics</td>
            <td>Term 1</td>
            <td>Sequence 1</td>
            <td>18.0</td>
            <td>2023-10-15</td>
        </tr>
    </tbody>
</table>

<?php
require_once '../../includes/footer.php';
?>