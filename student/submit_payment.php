<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../login.php");
    exit;
}
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];
    $dues = $_POST['dues'];
    $transaction_id = $_POST['transaction_id'];

    // Save transaction in the database
    $stmt = $conn->prepare("
        INSERT INTO Payments (student_id, course_id, amount, payment_date, payment_method, transaction_id) 
        VALUES (?, ?, ?, NOW(), 'UPI', ?)
    ");
    $stmt->execute([$student_id, $course_id, $dues, $transaction_id]);

    // Redirect back to payment status
    header("Location: payment_status.php");
    exit;
}
?>
