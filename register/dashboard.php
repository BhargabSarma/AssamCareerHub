<?php
session_start();
include '../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

// Fetch courses and student count for each course based on batches
$stmt = $conn->prepare(query: "
    SELECT c.course_id, c.course_name, c.duration, c.fee, 
           COUNT(sb.student_id) AS student_count 
    FROM Courses c 
    LEFT JOIN Batches b ON c.course_id = b.course_id 
    LEFT JOIN Student_batches sb ON b.batch_id = sb.batch_id 
    WHERE c.active = 1 
    GROUP BY c.course_id, c.course_name, c.duration, c.fee
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch student count by city
$stmt_city = $conn->prepare("SELECT city, COUNT(*) AS student_count FROM Students GROUP BY city");
$stmt_city->execute();
$city_data = $stmt_city->fetchAll(PDO::FETCH_ASSOC);

// Convert data for JavaScript
$city_labels = json_encode(array_column($city_data, 'city'));
$city_counts = json_encode(array_column($city_data, 'student_count'));

// Fetch student count by gender
$stmt_gender = $conn->prepare("SELECT gender, COUNT(*) AS count FROM Students GROUP BY gender");
$stmt_gender->execute();
$gender_data = $stmt_gender->fetchAll(PDO::FETCH_ASSOC);

// Convert data for JavaScript
$gender_labels = json_encode(array_column($gender_data, 'gender'));
$gender_counts = json_encode(array_column($gender_data, 'count'));
?>



<!DOCTYPE html>
<lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assam Career Hub</title>
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
                            <a class="nav-link btn btn-danger text-white" href="../logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container my-4">
            <h1 class="text-center">Register Dashboard</h1>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4>Total Courses</h4>
                        </div>
                        <div class="card-body">
                            <p class="fs-3"><?php echo count($courses); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4>Total Students</h4>
                        </div>
                        <div class="card-body">
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
            <div class="row mt-5">
                <!-- Bar Chart for Students in Different Cities -->
                <div class="col-md-6">
                    <h3 class="text-center">Students in Different Cities</h3>
                    <canvas id="cityChart"></canvas>
                </div>

                <!-- Pie Chart for Gender Distribution -->
                <div class="col-md-6">
                    <h3 class="text-center">Gender Distribution</h3>
                    <canvas id="genderChart"></canvas>
                </div>
            </div>


            <h2 class="mt-5">Courses Overview</h2>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Course Name</th>
                        <th>Duration</th>
                        <th>Fee</th>
                        <th>Number of Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo $course['course_name']; ?></td>
                            <td><?php echo $course['duration']; ?></td>
                            <td><?php echo $course['fee']; ?></td>
                            <td><?php echo $course['student_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Data from PHP
                const cityLabels = <?php echo $city_labels; ?>;
                const cityCounts = <?php echo $city_counts; ?>;
                const genderLabels = <?php echo $gender_labels; ?>;
                const genderCounts = <?php echo $gender_counts; ?>;

                // Bar Chart - Students in Different Cities
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

                // Pie Chart - Gender Distribution
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


        <?php include './footer.php'; ?>