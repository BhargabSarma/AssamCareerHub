<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

$error = '';
$success = '';

if (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
    $stmt = $conn->prepare("SELECT s.*, c.course_name, b.batch_name FROM Students s 
                            LEFT JOIN Student_Batches sb ON s.student_id = sb.student_id 
                            LEFT JOIN Batches b ON sb.batch_id = b.batch_id 
                            LEFT JOIN Courses c ON b.course_id = c.course_id 
                            WHERE s.student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $error = "Student not found!";
    }
} else {
    header("Location: manage_students.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $student['password'];

    try {
        $stmt = $conn->prepare("UPDATE Students SET name = ?, email = ?, phone = ?, password = ? WHERE student_id = ?");
        $stmt->execute([$name, $email, $phone, $password, $student_id]);

        if ($stmt->rowCount() > 0) {
            $success = "Student updated successfully!";
            $stmt = $conn->prepare("SELECT s.*, c.course_name, b.batch_name FROM Students s 
                                    LEFT JOIN Student_Batches sb ON s.student_id = sb.student_id 
                                    LEFT JOIN Batches b ON sb.batch_id = b.batch_id 
                                    LEFT JOIN Courses c ON b.course_id = c.course_id 
                                    WHERE s.student_id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "No changes were made.";
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assam Career Hub</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">Assam Career Hub Register</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">Dashboard</a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="./manage_students.php">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../payments/manage_payments.php">Payments</a>
                    </li>
                    <!-- <li class="nav-item">
                    <a class="nav-link" href="../allocated_courses.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../batches/manage_batches.php">Manage Batches</a>
                </li> -->
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white" href="../../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-center mb-4">Edit Student</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"> <?php echo $error; ?> </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"> <?php echo $success; ?> </div>
        <?php endif; ?>

        <?php if ($student): ?>
            <form method="POST" class="card shadow p-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password (Leave blank to keep existing):</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <!-- View-only Fields with Background Color Change -->
                <div class="mb-3">
                    <label class="form-label">Address:</label>
                    <input type="text" class="form-control view-only" value="<?php echo htmlspecialchars($student['address']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Course:</label>
                    <input type="text" class="form-control view-only" value="<?php echo htmlspecialchars($student['course_name']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Batch:</label>
                    <input type="text" class="form-control view-only" value="<?php echo htmlspecialchars($student['batch_name']); ?>" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="manage_students.php" class="btn btn-secondary">Back</a>
            </form>
        <?php endif; ?>
    </div>

    <?php include '../footer.php'; ?>

    <!-- Add this CSS to style view-only fields -->
    <style>
        .view-only {
            background-color: #f0f0f0;
            /* Light grey background for view-only fields */
            cursor: not-allowed;
            /* Show "not-allowed" cursor to indicate non-editable */
        }
    </style>