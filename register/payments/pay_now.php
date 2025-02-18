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
    SELECT p.*, s.name AS student_name, c.course_name, c.fee, p.booking_amount, p.total_paid AS total_paid, p.status 
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

// Calculate remaining fee
$remaining_fee = $payment['fee'] - $payment['total_paid'];

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pay_full'])) {
        $pay_amount = $remaining_fee;
        $status = "Fully Paid";
    } elseif (isset($_POST['pay_installment'])) {
        $pay_amount = $remaining_fee / 2;
        $status = "Partially Paid";
    }

    // Update the payment record
    $stmt = $conn->prepare("UPDATE Payments SET total_paid = total_paid + ?, status = ? WHERE payment_id = ?");
    $stmt->execute([$pay_amount, $status, $payment_id]);

    $_SESSION['success'] = "Payment successfully recorded!";
    header("Location: manage_payments.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assam Career Hub</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">Assam Career Hub Register</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">Dashboard</a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="../students/manage_students.php">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./manage_payments.php">Payments</a>
                    </li>
                    <!-- <li class="nav-item">
                    <a class="nav-link" href="../allocated_courses.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../batches/manage_batches.php">Manage Batches</a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="../../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container my-4">
        <h1 class="text-center">Pay Remaining Amount</h1>
        <p>Student: <strong><?php echo htmlspecialchars($payment['student_name']); ?></strong></p>
        <p>Course: <strong><?php echo htmlspecialchars($payment['course_name']); ?></strong></p>
        <p>Course Fee: <strong><?php echo number_format($payment['fee'], 2); ?></strong></p>
        <p>Booking Amount: <strong><?php echo number_format($payment['booking_amount'], 2); ?></strong></p>
        <p>Total Paid: <strong><?php echo number_format($payment['total_paid'], 2); ?></strong></p>
        <p>Remaining Fee: <strong><?php echo number_format($remaining_fee, 2); ?></strong></p>
        <p>Status:
            <?php
            if ($payment['status'] === 'Fully Paid') {
                echo '<span class="badge bg-success">Fully Paid</span>';
            } elseif ($payment['status'] === 'Partially Paid') {
                echo '<span class="badge bg-warning">Partially Paid</span>';
            } elseif ($payment['status'] === 'Pending') {
                echo '<span class="badge bg-danger">Pending</span>';
            } else {
                echo '<span class="badge bg-secondary">Unknown</span>';
            }
            ?>
        </p>

        <form method="POST">
            <?php if ($payment['status'] === 'Pending'): ?>
                <button type="submit" name="pay_installment" class="btn btn-primary mt-3">
                    Pay First Installment (<?php echo number_format($remaining_fee / 2, 2); ?>)
                </button>
                <button type="submit" name="pay_full" class="btn btn-success mt-3">
                    Pay Full Amount (<?php echo number_format($remaining_fee, 2); ?>)
                </button>
            <?php elseif ($payment['status'] === 'Partially Paid'): ?>
                <button type="submit" name="pay_full" class="btn btn-success mt-3">
                    Pay Remaining Amount (<?php echo number_format($remaining_fee, 2); ?>)
                </button>
            <?php else: ?>
                <button class="btn btn-secondary mt-3" disabled>Payment Completed</button>
            <?php endif; ?>

            <a href="manage_payments.php" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>

    <?php include '../footer.php'; ?>