<?php
include '../config.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$payments = $conn->query("
    SELECT p.payment_id, s.name AS student_name, c.course_name, p.amount, p.payment_date, p.payment_method 
    FROM Payments p
    JOIN Students s ON p.student_id = s.student_id
    JOIN Courses c ON p.course_id = c.course_id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Payment Details</title>
</head>

<body>
    <h2>Payment Details</h2>
    <table border="1">
        <tr>
            <th>Payment ID</th>
            <th>Student Name</th>
            <th>Course Name</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Method</th>
        </tr>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?php echo $payment['payment_id']; ?></td>
                <td><?php echo $payment['student_name']; ?></td>
                <td><?php echo $payment['course_name']; ?></td>
                <td><?php echo $payment['amount']; ?></td>
                <td><?php echo $payment['payment_date']; ?></td>
                <td><?php echo $payment['payment_method']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>