<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$student_id = $_GET['id'] ?? null;
$error = '';
$success = '';

if ($student_id) {
    // Fetch student details
    $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        header("Location: manage_students.php?error=Student not found");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $stmt_update = $conn->prepare("UPDATE Students SET name = ?, email = ?, phone = ?, address = ? WHERE student_id = ?");
        if ($stmt_update->execute([$name, $email, $phone, $address, $student_id])) {
            $success = 'Student updated successfully!';
        } else {
            $error = 'Failed to update student. Please try again.';
        }
    }
} else {
    header("Location: manage_students.php?error=Invalid student ID");
    exit;
}
?>

<?php include '../partials/header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Edit Student</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="p-4 border rounded shadow-sm bg-light">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea id="address" name="address" class="form-control"><?php echo htmlspecialchars($student['address']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../partials/footer.php'; ?>