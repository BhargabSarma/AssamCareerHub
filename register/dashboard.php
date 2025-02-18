<?php
session_start();
include '../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

// Fetch courses with student count and batch name
$stmt = $conn->prepare(" 
    SELECT c.course_id, c.course_name, c.duration, c.fee, b.batch_name, 
           COUNT(sb.student_id) AS student_count 
    FROM Courses c 
    LEFT JOIN Batches b ON c.course_id = b.course_id 
    LEFT JOIN Student_batches sb ON b.batch_id = sb.batch_id 
    WHERE c.active = 1 
    GROUP BY c.course_id, c.course_name, c.duration, c.fee, b.batch_name
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of students
$stmt_students = $conn->prepare("SELECT COUNT(*) AS total_students FROM Students");
$stmt_students->execute();
$student_count = $stmt_students->fetch(PDO::FETCH_ASSOC)['total_students'];

// Fetch total payments received
$stmt_payments = $conn->prepare("SELECT SUM(total_paid) AS total_payments FROM Payments");
$stmt_payments->execute();
$total_payments = $stmt_payments->fetch(PDO::FETCH_ASSOC)['total_payments'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Assam Career Hub Register</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./students/manage_students.php">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./payments/manage_payments.php">Payments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-center">Register Dashboard</h1>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Total Courses</h4>
                    </div>
                    <div class="card-body">
                        <p class="fs-3"><?php echo count($courses); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4>Total Students</h4>
                    </div>
                    <div class="card-body">
                        <p class="fs-3"><?php echo $student_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4>Total Payments Received</h4>
                    </div>
                    <div class="card-body">
                        <p class="fs-3">&#8377; <?php echo number_format($total_payments, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-5">Courses Overview</h2>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Course Name</th>
                    <th>Batch Name</th>
                    <th>Duration</th>
                    <th>Fee</th>
                    <th>Number of Students</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo $course['course_name']; ?></td>
                        <td><?php echo $course['batch_name'] ?: 'N/A'; ?></td>
                        <td><?php echo $course['duration']; ?></td>
                        <td>&#8377; <?php echo number_format($course['fee'], 2); ?></td>
                        <td><?php echo $course['student_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include './footer.php'; ?>