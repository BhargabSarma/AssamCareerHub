<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) { // Super Admin access
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

// Handle course activation/deactivation
if (isset($_GET['toggle']) && isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $toggle = intval($_GET['toggle']); // 1 for activate, 0 for deactivate

    try {
        $stmt = $conn->prepare("UPDATE Courses SET active = ? WHERE course_id = ?");
        $stmt->execute([$toggle, $course_id]);

        if ($stmt->rowCount() > 0) {
            $success = $toggle ? "Course activated successfully." : "Course deactivated successfully.";
        } else {
            $error = "Failed to update the course status.";
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}

// Fetch all courses
$stmt = $conn->prepare("SELECT * FROM Courses");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Manage Courses</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-3">
        <a href="add_course.php" class="btn btn-primary">Add New Course</a>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Course Name</th>
                <th>Description</th>
                <th>Duration</th>
                <th>Fee</th>
                <th>Status</th>
                <th>Batches</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $index => $course): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['description']); ?></td>
                    <td><?php echo htmlspecialchars($course['duration']); ?></td>
                    <td><?php echo htmlspecialchars($course['fee']); ?></td>
                    <td>
                        <?php echo $course['active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                    </td>
                    <td>
                        <a href="../batches/manage_batches.php ?course_id=<?php echo $course['course_id']; ?>" class="btn btn-info btn-sm">Manage Batches</a>
                    </td>
                    <td>
                        <a href="edit_course.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="?toggle=<?php echo $course['active'] ? 0 : 1; ?>&course_id=<?php echo $course['course_id']; ?>"
                            class="btn btn-<?php echo $course['active'] ? 'warning' : 'success'; ?> btn-sm">
                            <?php echo $course['active'] ? 'Deactivate' : 'Activate'; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/super_admin_footer.php'; ?>