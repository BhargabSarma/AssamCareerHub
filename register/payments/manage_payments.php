<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

// Fetch all payment details
$stmt = $conn->prepare("
    SELECT p.payment_id, s.name AS student_name, s.email, c.course_name, c.fee, p.booking_amount, 
           p.installment_1, p.installment_2, (p.booking_amount + p.installment_1 + p.installment_2) AS total_paid, 
           c.fee - (p.booking_amount + p.installment_1 + p.installment_2) AS remaining_fee,
           p.status
    FROM Payments p
    JOIN Students s ON p.student_id = s.student_id
    JOIN Courses c ON p.course_id = c.course_id
    ORDER BY p.payment_date DESC
");
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../header.php'; ?>

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
                    <td><?php echo $payment['fee']; ?></td>
                    <td><?php echo $payment['booking_amount']; ?></td>
                    <td><?php echo $payment['installment_1']; ?></td>
                    <td><?php echo $payment['installment_2']; ?></td>
                    <td><?php echo $payment['total_paid']; ?></td>
                    <td><?php echo $payment['remaining_fee']; ?></td>
                    <td><?php echo $payment['status'] === 'Paid' ? 'Paid' : $payment['remaining_fee']; ?></td>
                    <td>
                        <?php if ($payment['status'] === 'Paid'): ?>
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