<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch allocated courses
$stmt = $conn->prepare("SELECT sc.allocation_id, s.name as student_name, c.course_name, sc.allocation_date 
                        FROM Student_Courses sc
                        JOIN Students s ON sc.student_id = s.student_id
                        JOIN Courses c ON sc.course_id = c.course_id");
$stmt->execute();
$allocations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../partials/header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Allocated Courses</h1>
    <a href="allocate_student.php" class="btn btn-primary mb-3">Allocate Course</a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Allocation Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allocations as $index => $allocation): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($allocation['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($allocation['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($allocation['allocation_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/footer.php'; ?>