<?php
session_start();
include '../../config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if batch_id is provided
if (!isset($_GET['batch_id'])) {
    $_SESSION['error'] = "Invalid batch ID!";
    header("Location: manage_batches.php");
    exit;
}

$batch_id = $_GET['batch_id'];

// Fetch batch details
$stmt = $conn->prepare("SELECT * FROM batches WHERE batch_id = ?");
$stmt->execute([$batch_id]);
$batch = $stmt->fetch(PDO::FETCH_ASSOC);

// If batch not found, redirect
if (!$batch) {
    $_SESSION['error'] = "Batch not found!";
    header("Location: manage_batches.php");
    exit;
}

// Fetch active courses
$coursesStmt = $conn->prepare("SELECT course_id, course_name FROM Courses WHERE active = 1");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_name = $_POST['batch_name'];
    $course_id = $_POST['course_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $active = isset($_POST['active']) ? 1 : 0;

    try {
        // Update batch details
        $updateStmt = $conn->prepare("UPDATE batches SET batch_name = ?, course_id = ?, start_date = ?, end_date = ?, active = ? WHERE batch_id = ?");
        $updateStmt->execute([$batch_name, $course_id, $start_date, $end_date, $active, $batch_id]);

        $_SESSION['success'] = "Batch updated successfully!";
        header("Location: manage_batches.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating batch: " . $e->getMessage();
        header("Location: edit_batch.php?batch_id=" . $batch_id);
        exit;
    }
}
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Edit Batch</h1>

    <!-- Success/Error Alert -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="batch_name">Batch Name</label>
            <input type="text" id="batch_name" name="batch_name" class="form-control" value="<?php echo htmlspecialchars($batch['batch_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="course_id">Select Course</label>
            <select id="course_id" name="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" <?php echo ($course['course_id'] == $batch['course_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $batch['start_date']; ?>" required>
        </div>

        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $batch['end_date']; ?>" required>
        </div>

        <div class="form-group">
            <label for="active">Is Active?</label>
            <input type="checkbox" id="active" name="active" <?php echo ($batch['active'] == 1) ? 'checked' : ''; ?>>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Batch</button>
    </form>
</div>

<?php include '../includes/super_admin_footer.php'; ?>