<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

// Fetch courses for filtering
$courses_stmt = $conn->prepare("SELECT * FROM Courses WHERE active = 1");
$courses_stmt->execute();
$courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter variables
$selected_course = $_GET['course_id'] ?? '';
$selected_batch = $_GET['batch_id'] ?? '';

// Fetch batches based on selected course
$batches = [];
if (!empty($selected_course)) {
    $batches_stmt = $conn->prepare("SELECT * FROM Batches WHERE course_id = ? AND active = 1");
    $batches_stmt->execute([$selected_course]);
    $batches = $batches_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch students with batch and course details
$sql = "
    SELECT s.student_id, s.name, s.email, s.phone, s.address, 
           b.batch_name, c.course_name 
    FROM Students s
    JOIN Student_batches sb ON s.student_id = sb.student_id
    JOIN Batches b ON sb.batch_id = b.batch_id
    JOIN Courses c ON b.course_id = c.course_id
    WHERE 1=1";

$params = [];

if (!empty($selected_course)) {
    $sql .= " AND c.course_id = ?";
    $params[] = $selected_course;
}
if (!empty($selected_batch)) {
    $sql .= " AND b.batch_id = ?";
    $params[] = $selected_batch;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assam Career Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body> -->

<?php include '../header.php'; ?>
<div class="container my-4">
    <h1 class="text-center mb-4">Manage Students</h1>

    <div class="mb-3">
        <a href="add_student.php" class="btn btn-primary">Add New Student</a>
    </div>

    <!-- Filter Section -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <label for="course" class="form-label">Filter by Course:</label>
            <select name="course_id" id="course" class="form-control" onchange="this.form.submit()">
                <option value="">All Courses</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['course_id']; ?>" <?= ($selected_course == $course['course_id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="batch" class="form-label">Filter by Batch:</label>
            <select name="batch_id" id="batch" class="form-control" onchange="this.form.submit()" <?= empty($batches) ? 'disabled' : ''; ?>>
                <option value="">All Batches</option>
                <?php foreach ($batches as $batch): ?>
                    <option value="<?= $batch['batch_id']; ?>" <?= ($selected_batch == $batch['batch_id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($batch['batch_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Student Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($student['name']); ?></td>
                        <td><?= htmlspecialchars($student['email']); ?></td>
                        <td><?= htmlspecialchars($student['phone']); ?></td>
                        <td><?= htmlspecialchars($student['address']); ?></td>
                        <td><?= htmlspecialchars($student['course_name']); ?></td>
                        <td><?= htmlspecialchars($student['batch_name']); ?></td>
                        <td>
                            <a href="edit_student.php?id=<?= $student['student_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_student.php?student_id=<?= $student['student_id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this student?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>