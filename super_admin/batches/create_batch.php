<?php
session_start();  // Start the session
include '../../config.php';  // Include the database connection

// Enable error reporting (temporary for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Display success or error messages
if (isset($_SESSION['success'])) {
    $alert_type = 'success';
    $alert_message = $_SESSION['success'];
    unset($_SESSION['success']);
} elseif (isset($_SESSION['error'])) {
    $alert_type = 'error';
    $alert_message = $_SESSION['error'];
    unset($_SESSION['error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Getting form data
    $batch_name = $_POST['batch_name'];
    $course_id = $_POST['course_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $active = isset($_POST['active']) ? 1 : 0;  // Check if the batch is active (1 if checked)

    try {
        // Prepare SQL query to insert a new batch
        $stmt = $conn->prepare("INSERT INTO batches (batch_name, course_id, start_date, end_date, active) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$batch_name, $course_id, $start_date, $end_date, $active]);
        $_SESSION['success'] = "Batch created successfully!";
        header("Location: create_batch.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error creating batch: " . $e->getMessage();
        header("Location: create_batch.php");
        exit;
    }
}

// Fetch active courses
$coursesStmt = $conn->prepare("SELECT course_id, course_name FROM Courses WHERE active = '1'");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Create New Batch</h1>

    <!-- Success/Error Alert -->
    <div id="alert-box" class="alert <?php echo isset($alert_type) ? $alert_type : ''; ?>" style="display:<?php echo isset($alert_message) ? 'block' : 'none'; ?>">
        <?php echo isset($alert_message) ? $alert_message : ''; ?>
    </div>

    <form method="POST" id="add-batch-form">
        <div class="form-group">
            <label for="batch_name">Batch Name</label>
            <input type="text" id="batch_name" name="batch_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="course_id">Select Course</label>
            <select id="course_id" name="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="active">Is Active?</label>
            <input type="checkbox" id="active" name="active" checked>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Batch</button>
    </form>
</div>

<?php include '../includes/super_admin_footer.php'; ?>

<script>
    // JavaScript to handle alert visibility
    window.onload = function() {
        var alertBox = document.getElementById('alert-box');
        if (alertBox) {
            alertBox.style.display = 'block';
            setTimeout(function() {
                alertBox.style.display = 'none';
            }, 5000); // Hide alert after 5 seconds
        }
    };
</script>