<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) { // Ensure super admin is logged in
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $duration = trim($_POST['duration']);
    $fee = trim($_POST['fee']);
    $booking_amount = trim($_POST['booking_amount']);
    $active = 1; // By default, a new course is active

    // Input validation
    if (empty($course_name) || empty($duration) || empty($fee) || empty($booking_amount)) {
        $error = 'Course name, duration, fee, and booking amount are required.';
    } elseif (!is_numeric($fee) || $fee <= 0) {
        $error = 'Fee must be a positive number.';
    } elseif (!is_numeric($booking_amount) || $booking_amount <= 0) {
        $error = 'Booking amount must be a positive number.';
    } elseif ($booking_amount > $fee) {
        $error = 'Booking amount cannot be greater than the course fee.';
    } else {
        // Insert course into database
        try {
            $stmt = $conn->prepare("INSERT INTO Courses (course_name, description, duration, fee, booking_amount, active) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$course_name, $description, $duration, $fee, $booking_amount, $active])) {
                $success = 'Course added successfully!';
            } else {
                $error = 'Failed to add the course. Please try again.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Add New Course</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="card shadow p-4">
        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name:</label>
            <input type="text" name="course_name" id="course_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration:</label>
            <input type="text" name="duration" id="duration" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="fee" class="form-label">Fee:</label>
            <input type="number" name="fee" id="fee" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="booking_amount" class="form-label">Booking Amount:</label>
            <input type="number" name="booking_amount" id="booking_amount" class="form-control" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success">Add Course</button>
        <a href="manage_courses.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include '../includes/super_admin_footer.php'; ?>