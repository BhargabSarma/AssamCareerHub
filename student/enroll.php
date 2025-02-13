<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: ./student_login.php");
    exit;
}
include '../config.php';

// Get the student ID from session
$student_id = $_SESSION['student_id'];

// Fetch enrolled courses with additional details
$stmt = $conn->prepare("
    SELECT c.course_id, c.course_name, c.fee, c.duration, b.start_date
    FROM student_batches sb
    INNER JOIN batches b ON sb.batch_id = b.batch_id
    INNER JOIN courses c ON b.course_id = c.course_id
    WHERE sb.student_id = ?
");
$stmt->execute([$student_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrolled Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .course-card {
            transition: 0.3s;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
<?php include 'student_nevbar.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">My Enrolled Courses</h2>
        <p class="text-muted">Explore the courses you are enrolled in.</p>
    </div>

    <div class="row">
        <?php if (count($courses) > 0) : ?>
            <?php foreach ($courses as $course) : ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card course-card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                📚 <?php echo htmlspecialchars($course['course_name']); ?>
                            </h5>
                            <p class="card-text text-muted">
                                <strong>Duration:</strong> <?php echo htmlspecialchars($course['duration']); ?> months
                            </p>
                            <p class="card-text text-muted">
                                <strong>Start Date:</strong> <?php echo htmlspecialchars($course['start_date']); ?>
                            </p>
                            <p class="card-text fw-bold">
                                💰 Fee: ₹<?php echo htmlspecialchars($course['fee']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12 text-center">
                <p class="text-danger fs-5">You are not enrolled in any courses.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
