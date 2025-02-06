<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

// Get the course details
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $stmt = $conn->prepare("SELECT * FROM Courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        $error = "Course not found!";
    }
} else {
    header("Location: manage_courses.php");
    exit;
}

// Handle course update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $fee = $_POST['fee'];

    if (empty($course_name) || empty($fee)) {
        $error = "Course Name and Fee are required.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE Courses SET course_name = ?, description = ?, duration = ?, fee = ? WHERE course_id = ?");
            $stmt->execute([$course_name, $description, $duration, $fee, $course_id]);

            if ($stmt->rowCount() > 0) {
                $success = "Course updated successfully!";
                // Refresh the course details
                $stmt = $conn->prepare("SELECT * FROM Courses WHERE course_id = ?");
                $stmt->execute([$course_id]);
                $course = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "No changes were made.";
            }
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Edit Course</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($course): ?>
        <form method="POST" class="card shadow p-4">
            <div class="mb-3">
                <label for="course_name" class="form-label">Course Name*:</label>
                <input type="text" name="course_name" id="course_name" class="form-control" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea name="description" id="description" class="form-control" rows="3"><?php echo htmlspecialchars($course['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration:</label>
                <input type="text" name="duration" id="duration" class="form-control" value="<?php echo htmlspecialchars($course['duration']); ?>">
            </div>
            <div class="mb-3">
                <label for="fee" class="form-label">Fee*:</label>
                <input type="number" step="0.01" name="fee" id="fee" class="form-control" value="<?php echo htmlspecialchars($course['fee']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Course</button>
            <a href="manage_courses.php" class="btn btn-secondary">Back</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/super_admin_footer.php'; ?>