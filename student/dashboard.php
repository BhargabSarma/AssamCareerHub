<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./student_login.php");
    exit;
}
include '../config.php';

$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- External CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-light">
    <?php include 'student_nevbar.php'; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm p-4 w-100" style="max-width: 500px;">
            <h2 class="text-center">Welcome, <?php echo htmlspecialchars($student['name']); ?> 👋</h2>
            <p class="text-muted text-center">What would you like to do today?</p>

            <div class="d-grid gap-3">
                <a href="enroll.php" class="btn btn-primary">📚 Enroll in Courses</a>
                <a href="payment_status.php" class="btn btn-success">💳 View Payment Status</a>
                <a href="../logout.php" class="btn btn-danger">🚪 Logout</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
