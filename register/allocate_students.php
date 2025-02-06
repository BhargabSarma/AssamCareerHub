<?php
session_start();
include '../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch all active courses
$coursesStmt = $conn->prepare("SELECT course_id, course_name FROM Courses WHERE active = '1'");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch students for a specific course if selected
$allocatedStudents = [];
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $studentsStmt = $conn->prepare("
        SELECT s.student_id, s.name, s.email, s.phone 
        FROM Students s
        JOIN Student_Courses sc ON s.student_id = sc.student_id
        WHERE sc.course_id = ?
    ");
    $studentsStmt->execute([$course_id]);
    $allocatedStudents = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include '../partials/header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Allocated Students by Course</h1>

    <form method="GET" class="mb-4">
        <div class="form-group">
            <label for="course_id">Select a Course</label>
            <select id="course_id" name="course_id" class="form-control">
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"
                        <?php echo (isset($_GET['course_id']) && $_GET['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Show Students</button>
    </form>

    <?php if (isset($_GET['course_id'])): ?>
        <h3>Allocated Students for Course:
            <?php
            $selectedCourse = array_filter($courses, fn($c) => $c['course_id'] == $_GET['course_id']);
            echo htmlspecialchars(current($selectedCourse)['course_name']);
            ?>
        </h3>
        <?php if (count($allocatedStudents) > 0): ?>
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allocatedStudents as $index => $student): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-warning mt-3">No students allocated to this course.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../partials/footer.php'; ?>