<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

// Handle activation/deactivation
if (isset($_GET['toggle']) && isset($_GET['register_id'])) {
    $register_id = intval($_GET['register_id']);
    $toggle = intval($_GET['toggle']); // 1 for activate, 0 for deactivate

    try {
        $stmt = $conn->prepare("UPDATE Register SET active = ? WHERE register_id = ?");
        $stmt->execute([$toggle, $register_id]);
        $success = $toggle ? "Register activated successfully." : "Register deactivated successfully.";
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}

// Fetch all registers
$stmt = $conn->prepare("SELECT * FROM Register");
$stmt->execute();
$registers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Manage Registers</h1>
    <a href="add_register.php" class="btn btn-primary mb-3">Add New Register</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registers as $index => $register): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($register['name']); ?></td>
                    <td><?php echo htmlspecialchars($register['email']); ?></td>
                    <td><?php echo htmlspecialchars($register['phone']); ?></td>
                    <td>
                        <?php echo $register['active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                    </td>
                    <td>
                        <a href="edit_register.php?register_id=<?php echo $register['register_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="?toggle=<?php echo $register['active'] ? 0 : 1; ?>&register_id=<?php echo $register['register_id']; ?>"
                            class="btn btn-<?php echo $register['active'] ? 'warning' : 'success'; ?> btn-sm">
                            <?php echo $register['active'] ? 'Deactivate' : 'Activate'; ?>
                        </a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../partials/footer.php'; ?>