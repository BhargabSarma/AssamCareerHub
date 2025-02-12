<?php
session_start();
include '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process payment here
    // Update the status of the payment
    $student_id = $_POST['student_id'];
    $payment_id = $_POST['payment_id']; // Add payment ID to form

    $stmt = $conn->prepare("UPDATE Payments SET status = 'Paid' WHERE payment_id = ?");
    $stmt->execute([$payment_id]);

    $_SESSION['success'] = "Payment successful for student ID: $student_id";
    header("Location: manage_payments.php");
    exit;
}
