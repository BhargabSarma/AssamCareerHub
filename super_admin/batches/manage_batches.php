<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

// Fetch batches and related course data
try {
    $stmt = $conn->prepare("SELECT b.batch_id, b.batch_name, b.start_date, b.end_date, b.active, c.course_name 
                            FROM batches b
                            JOIN courses c ON b.course_id = c.course_id");
    $stmt->execute();
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Failed to fetch batches: ' . $e->getMessage();
}
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Manage Batches</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="mb-3 text-end">
        <a href="create_batch.php" class="btn btn-primary">Add New Batch</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Batch Name</th>
                <th>Course Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($batches as $index => $batch): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($batch['batch_name']); ?></td>
                    <td><?php echo htmlspecialchars($batch['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($batch['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($batch['end_date']); ?></td>
                    <td>
                        <?php echo $batch['active']
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>'; ?>
                    </td>
                    <td>
                        <a href="edit_batch.php?batch_id=<?php echo $batch['batch_id']; ?>" class="btn btn-primary btn-sm">
                            Edit
                        </a>
                        <a href="toggle_batch_status.php?batch_id=<?php echo $batch['batch_id']; ?>&active=<?php echo $batch['active'] ? 0 : 1; ?>"
                            class="btn btn-<?php echo $batch['active'] ? 'danger' : 'success'; ?> btn-sm">
                            <?php echo $batch['active'] ? 'Deactivate' : 'Activate'; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/super_admin_footer.php'; ?>