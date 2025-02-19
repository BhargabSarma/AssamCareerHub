<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

if (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);

    try {
        // Start transaction
        $conn->beginTransaction();
        // Delete related records in Payments (if exists)
        $stmt = $conn->prepare("DELETE FROM Payments WHERE student_id = ?");
        $stmt->execute([$student_id]);

        // Delete related records in Student_batches
        $stmt = $conn->prepare("DELETE FROM Student_batches WHERE student_id = ?");
        $stmt->execute([$student_id]);

        // Delete the student
        $stmt = $conn->prepare("DELETE FROM Students WHERE student_id = ?");
        $stmt->execute([$student_id]);

        // Commit transaction
        $conn->commit();

        $_SESSION['success'] = "Student deleted successfully.";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: manage_students.php");
exit;
