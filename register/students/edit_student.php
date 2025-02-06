<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

$error = '';
$success = '';

// Get the student details
if (isset($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']);
    $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $error = "Student not found!";
    }
} else {
    header("Location: manage_students.php");
    exit;
}

// Handle student update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $address = "$city, $state"; // Combine state & city into address
    $course_id = $_POST['course_id'];
    $batch_id = $_POST['batch_id'];

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        try {
            // Update student details
            $stmt = $conn->prepare("UPDATE Students SET name = ?, email = ?, phone = ?, address = ? WHERE student_id = ?");
            $stmt->execute([$name, $email, $phone, $address, $student_id]);

            // Update batch assignment
            $stmt = $conn->prepare("DELETE FROM Student_Batches WHERE student_id = ?");
            $stmt->execute([$student_id]);

            if (!empty($batch_id)) {
                $stmt = $conn->prepare("INSERT INTO Student_Batches (student_id, batch_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $batch_id]);
            }

            $success = "Student details updated successfully!";
            // Refresh the student details
            $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}

// Fetch active courses
$coursesStmt = $conn->prepare("SELECT course_id, course_name FROM Courses WHERE active = '1'");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch active batches for the student's course
$batchesStmt = $conn->prepare("SELECT batch_id, batch_name FROM Batches WHERE course_id = (SELECT course_id FROM Students WHERE student_id = ?) AND active = '1'");
$batchesStmt->execute([$student_id]);
$batches = $batchesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Edit Student</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($student): ?>
        <form method="POST" class="card shadow p-4">
            <div class="mb-3">
                <label for="name" class="form-label">Student Name*:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email*:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone:</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
            <div class="mb-3">
                <label for="state" class="form-label">State:</label>
                <input type="text" name="state" id="state" class="form-control" value="<?php echo htmlspecialchars(explode(',', $student['address'])[1] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">City:</label>
                <input type="text" name="city" id="city" class="form-control" value="<?php echo htmlspecialchars(explode(',', $student['address'])[0] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="course_id" class="form-label">Select Course:</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>" <?php echo $student['course_id'] == $course['course_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Batches dropdown based on course selection -->
            <div class="mb-3" id="batch-container">
                <label for="batch_id" class="form-label">Select Batch:</label>
                <select name="batch_id" id="batch_id" class="form-control" required>
                    <option value="">-- Select Batch --</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?php echo $batch['batch_id']; ?>" <?php echo $student['batch_id'] == $batch['batch_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($batch['batch_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Student</button>
            <a href="manage_students.php" class="btn btn-secondary">Back</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>