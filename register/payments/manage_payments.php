<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

// Fetch all payment details
$stmt = $conn->prepare("
    SELECT p.payment_id, s.name AS student_name, s.email, c.course_name, 
           ROUND(c.fee, 2) AS fee, 
           ROUND(p.booking_amount, 2) AS booking_amount, 
           ROUND((c.fee - p.booking_amount) / 2, 2) AS installment_1, 
           ROUND((c.fee - p.booking_amount) / 2, 2) AS installment_2, 
           ROUND(p.total_paid, 2) AS total_paid, 
           ROUND(c.fee - p.total_paid, 2) AS remaining_fee, 
           p.status
    FROM Payments p
    JOIN Students s ON p.student_id = s.student_id
    JOIN Courses c ON p.course_id = c.course_id
    ORDER BY p.payment_date DESC
");
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="text-center mb-4">Manage Payments</h1>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Course Fee</th>
                    <th>Booking Amount</th>
                    <th>1st Installment</th>
                    <th>2nd Installment</th>
                    <th>Total Paid</th>
                    <th>Remaining Fee</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($payment['email']); ?></td>
                        <td><?php echo htmlspecialchars($payment['course_name']); ?></td>
                        <td><?php echo number_format($payment['fee'], 2); ?></td>
                        <td><?php echo number_format($payment['booking_amount'], 2); ?></td>
                        <td><?php echo number_format($payment['installment_1'], 2); ?></td>
                        <td><?php echo number_format($payment['installment_2'], 2); ?></td>
                        <td><?php echo number_format($payment['total_paid'], 2); ?></td>
                        <td><?php echo number_format($payment['remaining_fee'], 2); ?></td>
                        <td>
                            <?php
                            if ($payment['status'] === 'Pending') {
                                echo '<span class="badge bg-warning">Pending</span>';
                            } elseif ($payment['status'] === 'Partially Paid') {
                                echo '<span class="badge bg-info">Partially Paid</span>';
                            } elseif ($payment['status'] === 'Fully Paid') {
                                echo '<span class="badge bg-success">Fully Paid</span>';
                            } else {
                                echo '<span class="badge bg-secondary">Unknown</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($payment['status'] === 'Fully Paid'): ?>
                                <button class="btn btn-secondary btn-sm" disabled>Paid</button>
                            <?php elseif ($payment['remaining_fee'] > 0): ?>
                                <a href="pay_now.php?payment_id=<?php echo $payment['payment_id']; ?>" class="btn btn-success btn-sm">Pay Now</a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>Paid</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include '../footer.php'; ?>