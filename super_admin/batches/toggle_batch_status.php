<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

if (isset($_GET['batch_id']) && isset($_GET['active'])) {
    $batch_id = intval($_GET['batch_id']);
    $active = intval($_GET['active']); // 1 for activate, 0 for deactivate

    try {
        $stmt = $conn->prepare("UPDATE batches SET active = ? WHERE batch_id = ?");
        $stmt->execute([$active, $batch_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = $active ? 'Batch activated successfully!' : 'Batch deactivated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update batch status.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
    }
}

header("Location: manage_batches.php");
exit;
