<?php
session_start();
include '../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

// Fetch courses, batch names, and student count for each course
$stmt = $conn->prepare("SELECT c.course_id, c.course_name, c.duration, c.fee, b.batch_name, COUNT(sb.student_id) AS student_count FROM Courses c LEFT JOIN Batches b ON c.course_id = b.course_id LEFT JOIN Student_batches sb ON b.batch_id = sb.batch_id WHERE c.active = 1 GROUP BY c.course_id, c.course_name, c.duration, c.fee, b.batch_name");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch student count by city
$stmt_city = $conn->prepare("SELECT city, COUNT(*) AS student_count FROM Students GROUP BY city");
$stmt_city->execute();
$city_data = $stmt_city->fetchAll(PDO::FETCH_ASSOC);
$city_labels = json_encode(array_column($city_data, 'city'));
$city_counts = json_encode(array_column($city_data, 'student_count'));

// Fetch student count by gender
$stmt_gender = $conn->prepare("SELECT gender, COUNT(*) AS count FROM Students GROUP BY gender");
$stmt_gender->execute();
$gender_data = $stmt_gender->fetchAll(PDO::FETCH_ASSOC);
$gender_labels = json_encode(array_column($gender_data, 'gender'));
$gender_counts = json_encode(array_column($gender_data, 'count'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Assam Career Hub Super Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-center mb-4">Super Admin Dashboard</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                        <h5>Total Courses</h5>
                        <p class="fs-3"><?php echo count($courses); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <h5>Total Students</h5>
                        <?php
                        $stmt_students = $conn->prepare("SELECT COUNT(*) AS total_students FROM Students");
                        $stmt_students->execute();
                        $student_count = $stmt_students->fetch(PDO::FETCH_ASSOC)['total_students'];
                        ?>
                        <p class="fs-3"><?php echo $student_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container my-4">

            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Manage Courses</h5>
                            <p class="card-text">Add, edit, or deactivate courses.</p>
                            <a href="./courses/manage_courses.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Manage Batches</h5>
                            <p class="card-text">Add, edit, or deactivate batches for courses.</p>
                            <a href="./batches/manage_batches.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">View Payments</h5>
                            <p class="card-text">Can View all the transactions.</p>
                            <a href="./payments/view_payments.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Manage Registers</h5>
                            <p class="card-text">Create or manage registers (sub-admins).</p>
                            <a href="./registers/manage_register.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container my-4">
            <div class="row mt-5">
                <div class="col-md-7">
                    <h3 class="text-center">Students in Different Cities</h3>
                    <canvas id="cityChart"></canvas>
                </div>
                <div class="col-md-5">
                    <h3 class="text-center">Gender Distribution</h3>
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>


        <h2 class="mt-5">Courses Overview</h2>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Course Name</th>
                    <th>Duration</th>
                    <th>Fee</th>
                    <th>Batch Name</th>
                    <th>Number of Students</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo $course['course_name']; ?></td>
                        <td><?php echo $course['duration']; ?></td>
                        <td><?php echo $course['fee']; ?></td>
                        <td><?php echo $course['batch_name'] ? $course['batch_name'] : 'N/A'; ?></td>
                        <td><?php echo $course['student_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cityLabels = <?php echo $city_labels; ?>;
            const cityCounts = <?php echo $city_counts; ?>;
            const genderLabels = <?php echo $gender_labels; ?>;
            const genderCounts = <?php echo $gender_counts; ?>;

            new Chart(document.getElementById("cityChart"), {
                type: "bar",
                data: {
                    labels: cityLabels,
                    datasets: [{
                        label: "Students",
                        data: cityCounts,
                        backgroundColor: "rgba(54, 162, 235, 0.6)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            new Chart(document.getElementById("genderChart"), {
                type: "pie",
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: genderCounts,
                        backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"],
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });
    </script>

    <?php include './includes/super_admin_footer.php'; ?>