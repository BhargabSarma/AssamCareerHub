<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

if (!isset($_GET['payment_id'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: manage_payments.php");
    exit;
}

$payment_id = $_GET['payment_id'];

// Fetch payment details
$stmt = $conn->prepare("
    SELECT p.*, s.name AS student_name, c.course_name, c.fee, c.booking_amount
    FROM Payments p
    JOIN Students s ON p.student_id = s.student_id
    JOIN Courses c ON p.course_id = c.course_id
    WHERE p.payment_id = ?
");
$stmt->execute([$payment_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    $_SESSION['error'] = "Payment record not found!";
    header("Location: manage_payments.php");
    exit;
}

// Calculate the remaining fee
$total_paid = $payment['booking_amount'] + $payment['installment_1'] + $payment['installment_2'];
$remaining_fee = $payment['fee'] - $total_paid;

// Handle POST requests for payments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pay_remaining'])) {
        $pay_amount = $remaining_fee;
        $status = "Paid";
        // Update payment record to reflect full payment
        $stmt = $conn->prepare("UPDATE Payments SET installment_2 = ?, status = ? WHERE payment_id = ?");
        $stmt->execute([$pay_amount, $status, $payment_id]);

        // Update remaining fee to 0
        $remaining_fee = 0;
    } elseif (isset($_POST['pay_installment'])) {
        $pay_amount = 0;
        if ($payment['installment_1'] == 0) {
            $pay_amount = $remaining_fee; // Pay 1st installment
            $status = "Partially Paid";
            $stmt = $conn->prepare("UPDATE Payments SET installment_1 = ?, status = ? WHERE payment_id = ?");
            $stmt->execute([$pay_amount, $status, $payment_id]);
        } elseif ($payment['installment_1'] > 0 && $payment['installment_2'] == 0) {
            // Pay 2nd installment
            $pay_amount = $remaining_fee;
            $stmt = $conn->prepare("UPDATE Payments SET installment_2 = ?, status = 'Paid' WHERE payment_id = ?");
            $stmt->execute([$pay_amount, $payment_id]);
            $status = "Paid";
        }

        $_SESSION['success'] = "Payment successfully recorded!";
        header("Location: manage_payments.php");
        exit;
    }
}
?>

<?php include '../header.php'; ?>

<div class="container my-4">
    <h1 class="text-center">Pay Remaining Amount</h1>
    <p>Student: <strong><?php echo htmlspecialchars($payment['student_name']); ?></strong></p>
    <p>Course: <strong><?php echo htmlspecialchars($payment['course_name']); ?></strong></p>
    <p>Course Fee: <strong><?php echo $payment['fee']; ?></strong></p>
    <p>Total Paid: <strong><?php echo $total_paid; ?></strong></p>
    <p>Remaining Fee: <strong><?php echo $remaining_fee; ?></strong></p>

    <h3>Installment Details:</h3>
    <p>Booking Amount: <strong><?php echo $payment['booking_amount']; ?></strong></p>
    <p>1st Installment: <strong><?php echo $payment['installment_1']; ?></strong></p>
    <p>2nd Installment: <strong><?php echo $payment['installment_2']; ?></strong></p>
    <p>Status:
        <?php
        if ($payment['status'] === 'Paid') {
            echo '<span class="badge bg-success">Paid</span>';
        } elseif ($payment['installment_1'] > 0 && $payment['installment_2'] == 0) {
            echo '<span class="badge bg-warning">Partially Paid (1st installment done)</span>';
        } elseif ($payment['installment_1'] == 0) {
            echo '<span class="badge bg-info">Booking Amount Paid</span>';
        } else {
            echo '<span class="badge bg-secondary">Unpaid</span>';
        }
        ?>
    </p>

    <form method="POST">
        <!-- Display custom payment buttons based on installment status -->
        <?php if ($payment['installment_1'] == 0): ?>
            <button type="submit" name="pay_installment" class="btn btn-primary mt-3">
                Pay First Installment
            </button>
        <?php elseif ($payment['installment_1'] > 0 && $payment['installment_2'] == 0): ?>
            <button type="submit" name="pay_installment" class="btn btn-primary mt-3">
                Pay Second Installment
            </button>
        <?php else: ?>
            <button type="submit" name="pay_remaining" class="btn btn-success mt-3">
                Pay Remaining Amount (Full Payment)
            </button>
        <?php endif; ?>

        <a href="manage_payments.php" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<?php include '../footer.php'; ?>