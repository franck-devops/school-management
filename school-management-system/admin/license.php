<?php
require_once '../../includes/header.php';
requireLogin();
checkRole(['principal']);

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = sanitize($_POST['payment_method']);
    $phoneNumber = sanitize($_POST['phone_number']);
    $amount = 200000;
    
    // Simulate API call
    $transactionId = 'TXN' . time();
    $status = 'pending';
    
    // Save to database
    try {
        $stmt = $pdo->prepare("INSERT INTO license_payments (user_id, amount, payment_method, phone_number, transaction_id, status) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $amount, $paymentMethod, $phoneNumber, $transactionId, $status]);
        
        // Simulate successful payment after 5 seconds
        // In real implementation, you would use webhooks to verify payment
        $_SESSION['payment_message'] = 'Payment initiated. Transaction ID: ' . $transactionId;
    } catch (PDOException $e) {
        $_SESSION['payment_message'] = 'Error processing payment: ' . $e->getMessage();
    }
    
    header('Location: license.php');
    exit();
}

// Check for payment message
$paymentMessage = '';
if (isset($_SESSION['payment_message'])) {
    $paymentMessage = $_SESSION['payment_message'];
    unset($_SESSION['payment_message']);
}
?>

<h2>License Payment</h2>

<?php if ($paymentMessage): ?>
    <div class="alert alert-success"><?= $paymentMessage ?></div>
<?php endif; ?>

<div class="payment-form">
    <form method="POST" action="">
        <div>
            <label>Amount:</label>
            <input type="text" value="200000 XAF" readonly>
        </div>
        <div>
            <label>Payment Method:</label>
            <select name="payment_method" required>
                <option value="">Select method</option>
                <option value="mtn">MTN Mobile Money (671704285)</option>
                <option value="orange">Orange Money (657649511)</option>
            </select>
        </div>
        <div>
            <label>Your Phone Number:</label>
            <input type="text" name="phone_number" required placeholder="e.g., 671704285">
        </div>
        <div>
            <button type="submit">Make Payment</button>
        </div>
    </form>
</div>

<div class="payment-instructions">
    <h3>Payment Instructions:</h3>
    <ol>
        <li>Select your payment method (MTN or Orange Money)</li>
        <li>Enter your phone number</li>
        <li>Click "Make Payment"</li>
        <li>You will receive a payment request on your phone</li>
        <li>Complete the payment on your mobile device</li>
    </ol>
</div>

<?php
require_once '../../includes/footer.php';
?>