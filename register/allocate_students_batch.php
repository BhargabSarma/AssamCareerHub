<?php
session_start();
include '../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register.php");
    exit;
}

// Fetch courses
$courses = $conn->query("SELECT * FROM Courses WHERE active = '1'")->fetchAll(PDO::FETCH_ASSOC);

$selected_course = $_POST['course_id'] ?? null;
$selected_batch = $_POST['batch_id'] ?? null;
$students = [];
$batches = [];

if ($selected_course) {
    // Fetch batches for the selected course
    $stmt = $conn->prepare("SELECT * FROM Batches WHERE course_id = ? AND active = 1");
    $stmt->execute([$selected_course]);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($selected_batch) {
        // Fetch students not already allocated to the batch
        $stmt = $conn->prepare("
            SELECT * FROM Students WHERE student_id NOT IN (
                SELECT student_id FROM Student_Batches WHERE batch_id = ?
            )
        ");
        $stmt->execute([$selected_batch]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_ids'])) {
    $student_ids = $_POST['student_ids'];

    foreach ($student_ids as $student_id) {
        $stmt = $conn->prepare("INSERT INTO Student_Batches (batch_id, student_id) VALUES (?, ?)");
        $stmt->execute([$selected_batch, $student_id]);
    }

    $_SESSION['success'] = "Students allocated to batch successfully!";
    header("Location: allocate_students_batch.php");
    exit;
}

include '../partials/header.php';
?>

<div class="container my-4">
    <h1 class="text-center">Allocate Students to a Batch</h1>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="course_id" class="form-label">Select Course</label>
            <select class="form-control" id="course_id" name="course_id" onchange="this.form.submit()" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" <?php echo $course['course_id'] == $selected_course ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($batches): ?>
            <div class="mb-3">
                <label for="batch_id" class="form-label">Select Batch</label>
                <select class="form-control" id="batch_id" name="batch_id" onchange="this.form.submit()" required>
                    <option value="">-- Select Batch --</option>
                    <?php foreach ($batches as $batch): ?>
                        <option value="<?php echo $batch['batch_id']; ?>" <?php echo $batch['batch_id'] == $selected_batch ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($batch['batch_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($students): ?>
            <div class="mb-3">
                <label for="student_ids" class="form-label">Select Students</label>
                <select class="form-control" id="student_ids" name="student_ids[]" multiple required>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['student_id']; ?>">
                            <?php echo htmlspecialchars($student['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Allocate Students</button>
        <?php endif; ?>
    </form>
</div>

<?php include '../partials/footer.php'; ?>