<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['bourser']);

// Handle fee payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_payment'])) {
    $studentId = sanitize($_POST['student_id']);
    $amount = sanitize($_POST['amount']);
    $paymentMethod = sanitize($_POST['payment_method']);
    $receiptNumber = sanitize($_POST['receipt_number']);
    $paymentDate = sanitize($_POST['payment_date']) ?: date('Y-m-d');
    
    try {
        $stmt = $pdo->prepare("INSERT INTO fee_payments (student_id, amount, payment_method, receipt_number, payment_date, recorded_by) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentId, $amount, $paymentMethod, $receiptNumber, $paymentDate, $_SESSION['user_id']]);
        
        $_SESSION['message'] = 'Fee payment recorded successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error recording payment: ' . $e->getMessage();
    }
    
    header('Location: fees.php');
    exit();
}

// Handle fee structure update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_fee_structure'])) {
    $classId = sanitize($_POST['class_id']);
    $amount = sanitize($_POST['amount']);
    $description = sanitize($_POST['description']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO fee_structure (class_id, amount, description) 
                              VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE amount = ?, description = ?");
        $stmt->execute([$classId, $amount, $description, $amount, $description]);
        
        $_SESSION['message'] = 'Fee structure updated successfully';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Error updating fee structure: ' . $e->getMessage();
    }
    
    header('Location: fees.php');
    exit();
}

// Get message if exists
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Get all students and classes
$students = getStudents();
$classes = getClasses();
?>

<h2>Fee Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="tabs">
    <button class="tab-btn active" onclick="openTab('payment')">Record Payment</button>
    <button class="tab-btn" onclick="openTab('structure')">Fee Structure</button>
    <button class="tab-btn" onclick="openTab('reports')">Payment Reports</button>
</div>

<div id="payment" class="tab-content" style="display: block;">
    <h3>Record Fee Payment</h3>
    <form method="POST" action="">
        <input type="hidden" name="record_payment" value="1">
        <div>
            <label>Student:</label>
            <select name="student_id" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>">
                        <?= $student['first_name'] . ' ' . $student['last_name'] ?> 
                        (<?= $student['admission_number'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Amount (XAF):</label>
            <input type="number" name="amount" min="0" step="500" required>
        </div>
        <div>
            <label>Payment Method:</label>
            <select name="payment_method" required>
                <option value="cash">Cash</option>
                <option value="mtn">MTN Mobile Money</option>
                <option value="orange">Orange Money</option>
                <option value="bank">Bank Transfer</option>
            </select>
        </div>
        <div>
            <label>Receipt Number:</label>
            <input type="text" name="receipt_number" required>
        </div>
        <div>
            <label>Payment Date:</label>
            <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>">
        </div>
        <div>
            <button type="submit">Record Payment</button>
        </div>
    </form>
</div>

<div id="structure" class="tab-content">
    <h3>Fee Structure</h3>
    <form method="POST" action="">
        <input type="hidden" name="update_fee_structure" value="1">
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
            <label>Amount (XAF):</label>
            <input type="number" name="amount" min="0" step="500" required>
        </div>
        <div>
            <label>Description:</label>
            <input type="text" name="description" placeholder="e.g., Tuition fee, PTA fee, etc.">
        </div>
        <div>
            <button type="submit">Update Fee Structure</button>
        </div>
    </form>
    
    <h4>Current Fee Structure</h4>
    <table>
        <thead>
            <tr>
                <th>Class</th>
                <th>Amount (XAF)</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stmt = $pdo->query("SELECT fs.*, c.name as class_name 
                                FROM fee_structure fs 
                                JOIN classes c ON fs.class_id = c.id 
                                ORDER BY c.name");
            $feeStructure = $stmt->fetchAll();
            
            foreach ($feeStructure as $fee): ?>
                <tr>
                    <td><?= $fee['class_name'] ?></td>
                    <td><?= number_format($fee['amount']) ?></td>
                    <td><?= $fee['description'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="reports" class="tab-content">
    <h3>Payment Reports</h3>
    <form method="GET" action="" class="report-filters">
        <div>
            <label>Class:</label>
            <select name="class_id">
                <option value="">All Classes</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>" <?= isset($_GET['class_id']) && $_GET['class_id'] == $class['id'] ? 'selected' : '' ?>>
                        <?= $class['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>From Date:</label>
            <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>">
        </div>
        <div>
            <label>To Date:</label>
            <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>">
        </div>
        <div>
            <button type="submit">Filter</button>
            <button type="button" onclick="exportToExcel('payments-table', 'fee_payments')">Export to Excel</button>
        </div>
    </form>
    
    <table id="payments-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Class</th>
                <th>Amount (XAF)</th>
                <th>Method</th>
                <th>Receipt No.</th>
                <th>Recorded By</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT fp.*, s.first_name, s.last_name, s.admission_number, c.name as class_name, u.full_name as recorded_by 
                     FROM fee_payments fp 
                     JOIN students s ON fp.student_id = s.id 
                     JOIN classes c ON s.class_id = c.id 
                     JOIN users u ON fp.recorded_by = u.id";
            
            $conditions = [];
            $params = [];
            
            if (!empty($_GET['class_id'])) {
                $conditions[] = "s.class_id = ?";
                $params[] = $_GET['class_id'];
            }
            
            if (!empty($_GET['from_date'])) {
                $conditions[] = "fp.payment_date >= ?";
                $params[] = $_GET['from_date'];
            }
            
            if (!empty($_GET['to_date'])) {
                $conditions[] = "fp.payment_date <= ?";
                $params[] = $_GET['to_date'];
            }
            
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $query .= " ORDER BY fp.payment_date DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $payments = $stmt->fetchAll();
            
            foreach ($payments as $payment): ?>
                <tr>
                    <td><?= $payment['payment_date'] ?></td>
                    <td><?= $payment['first_name'] . ' ' . $payment['last_name'] ?> (<?= $payment['admission_number'] ?>)</td>
                    <td><?= $payment['class_name'] ?></td>
                    <td><?= number_format($payment['amount']) ?></td>
                    <td><?= ucfirst($payment['payment_method']) ?></td>
                    <td><?= $payment['receipt_number'] ?></td>
                    <td><?= $payment['recorded_by'] ?></td>
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