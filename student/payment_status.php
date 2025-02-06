<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../login.php");
    exit;
}
include '../config.php';

$student_id = $_SESSION['user_id'];
$upi_id = "upi@bank"; // Replace with actual UPI ID

// Fetch payment details for the student
$stmt = $conn->prepare("
    SELECT 
        c.course_name, 
        b.start_date, 
        b.end_date, 
        p.payment_type,
        p.amount, 
        p.payment_date, 
        p.payment_method,
        p.payment_id 
    FROM Payments p
    INNER JOIN batches b ON p.batch_id = b.batch_id
    INNER JOIN courses c ON b.course_id = c.course_id
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
            UPDATE Payments 
            SET payment_method = 'UPI', payment_date = NOW() 
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
    <div class="dashboard-container">
        <h2>Your Payment Status</h2>
        <p class="text-muted">Below is the record of your payments.</p>

        <?php if (isset($message)) : ?>
            <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Course Name</th>
                    <th>Batch Start</th>
                    <th>Batch End</th>
                    <th>Payment Type</th>
                    <th>Amount (₹)</th>
                    <th>Payment Date</th>
                    <th>Payment Method</th>
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
                            <td><?php echo htmlspecialchars($payment['payment_type']); ?></td>
                            <td>₹<?php echo htmlspecialchars($payment['amount']); ?></td>
                            <td><?php echo $payment['payment_date'] ? htmlspecialchars($payment['payment_date']) : '<span class="text-danger">Pending</span>'; ?></td>
                            <td><?php echo $payment['payment_method'] ? htmlspecialchars($payment['payment_method']) : '<span class="text-danger">Pending</span>'; ?></td>
                            <td>
                                <?php if (!$payment['payment_date']) : ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#payModal<?php echo $payment['payment_id']; ?>">
                                        Pay Now
                                    </button>

                                    <!-- Payment Modal -->
                                    <div class="modal fade" id="payModal<?php echo $payment['payment_id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pay Now - <?php echo htmlspecialchars($payment['course_name']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Amount:</strong> ₹<?php echo htmlspecialchars($payment['amount']); ?></p>
                                                    <p><strong>UPI ID:</strong> <span class="text-primary"><?php echo $upi_id; ?></span></p>
                                                    <p><strong>Scan QR Code:</strong></p>
                                                    <img src="../images/qr-code.png" alt="QR Code" class="img-fluid" width="200">
                                                    <hr>
                                                    <form method="POST">
                                                        <input type="hidden" name="payment_id" value="<?php echo $payment['payment_id']; ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Enter Transaction ID:</label>
                                                            <input type="text" name="transaction_id" class="form-control" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-success">Submit Payment</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <span class="text-success">Paid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="text-center">No payment records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
