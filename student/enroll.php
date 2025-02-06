<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'student') {
    header("Location: ../login.php");
    exit;
}
include '../config.php';

// Get the student ID from session
$student_id = $_SESSION['user_id'];

// Fetch enrolled courses using student_batches
$stmt = $conn->prepare("
    SELECT c.course_id, c.course_name, c.fee
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
</head>

<body>
    <div class="dashboard-container">
        <h2>My Enrolled Courses</h2>
        <p class="text-muted">Here are the courses you are enrolled in.</p>

        <?php if (count($courses) > 0) : ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td>₹<?php echo htmlspecialchars($course['fee']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p class="text-danger">You are not enrolled in any courses.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
