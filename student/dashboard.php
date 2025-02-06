<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ../student_login.php");
    exit;
}
include '../config.php';

$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Now include the header

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to External CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'student_nevbar.php'; ?>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?> 👋</h2>
        <p class="text-muted">What would you like to do today?</p>

        <div class="dashboard-links">
            <a href="enroll.php">📚 Enroll in Courses</a>
            <a href="payment_status.php">💳 View Payment Status</a>
            <a href="../logout.php">🚪 Logout</a>
        </div>
    </div>

    <!-- Bootstrap JS (Optional for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>