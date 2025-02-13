<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./student_login.php");
    exit;
}
include '../config.php';

$student_id = $_SESSION['student_id'];
$upi_id = "upi@bank"; // Replace with actual UPI ID

// Fetch payment details for the student
$stmt = $conn->prepare("
    SELECT 
        c.course_name, 
        b.start_date, 
        b.end_date, 
        p.payment_id,
        p.booking_amount,
        p.installment_1,
        p.installment_2,
        p.full_payment,
        p.status,
        p.payment_date
    FROM payments p
    INNER JOIN courses c ON p.course_id = c.course_id
    INNER JOIN batches b ON c.course_id = b.course_id
    WHERE p.student_id = ?
");
$stmt->execute([$student_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];
    $transaction_id = $_POST['transaction_id'];

    if (!empty($transaction_id)) {
        $updateStmt = $conn->prepare("
            UPDATE payments 
            SET payment_date = NOW(), status = 'Paid' 
            WHERE payment_id = ? AND student_id = ?
        ");
        $updateStmt->execute([$payment_id, $student_id]);
        $message = "Payment successfully recorded!";
    } else {
        $message = "Transaction ID is required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<?php include 'student_nevbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center text-primary">Your Payment Status</h2>
    <p class="text-center text-muted">Below is the record of your payments.</p>

    <?php if (isset($message)) : ?>
        <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-hover mt-3">
        <thead class="table-dark">
            <tr>
                <th>Course Name</th>
                <th>Batch Start</th>
                <th>Batch End</th>
                <th>Booking Amount</th>
                <th>Installment 1</th>
                <th>Installment 2</th>
                <th>Full Payment</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php if (count($payments) > 0) : ?>
        <?php foreach ($payments as $payment) : ?>
            <tr>
                <td><?php echo htmlspecialchars($payment['course_name']); ?></td>
                <td><?php echo htmlspecialchars($payment['start_date']); ?></td>
                <td><?php echo htmlspecialchars($payment['end_date']); ?></td>

                <!-- Booking Amount -->
                <td>
                    ₹<?php echo htmlspecialchars($payment['booking_amount']); ?><br>
                    <span class="badge <?php echo ($payment['booking_amount'] > 0 && $payment['status'] === 'Paid') ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo ($payment['booking_amount'] > 0 && $payment['status'] === 'Paid') ? 'Paid' : 'Pending'; ?>
                    </span>
                </td>

                <!-- Installment 1 -->
                <td>
                    ₹<?php echo htmlspecialchars($payment['installment_1']); ?><br>
                    <span class="badge <?php echo ($payment['installment_1'] > 0 && $payment['status'] === 'Paid') ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo ($payment['installment_1'] > 0 && $payment['status'] === 'Paid') ? 'Paid' : 'Pending'; ?>
                    </span>
                </td>

                <!-- Installment 2 -->
                <td>
                    ₹<?php echo htmlspecialchars($payment['installment_2']); ?><br>
                    <span class="badge <?php echo ($payment['installment_2'] > 0 && $payment['status'] === 'Paid') ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo ($payment['installment_2'] > 0 && $payment['status'] === 'Paid') ? 'Paid' : 'Pending'; ?>
                    </span>
                </td>

                <!-- Full Payment (No Status Badge) -->
                <td>₹<?php echo htmlspecialchars($payment['full_payment']); ?></td>

                <td>
                    <?php if ($payment['status'] !== 'Paid') : ?>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#payModal<?php echo $payment['payment_id']; ?>">
                            Pay Now
                        </button>
                    <?php else : ?>
                        <span class="text-success">✔ Payment Complete</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="9" class="text-center">No payment records found.</td>
        </tr>
    <?php endif; ?>
</tbody>

    </table>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
