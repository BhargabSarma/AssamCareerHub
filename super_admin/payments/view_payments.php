<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) { // Ensure super admin is logged in
    header("Location: super_admin_login.php");
    exit;
}

// Fetch courses for filters
$courses = $conn->query("SELECT course_id, course_name FROM Courses")->fetchAll(PDO::FETCH_ASSOC);

// Handle filtering
$whereClauses = [];
$params = [];

// Filter by course_id if provided
if (!empty($_GET['course_id'])) {
    $whereClauses[] = "p.course_id = ?";
    $params[] = $_GET['course_id'];
}

// Filter by batch_id if provided
if (!empty($_GET['batch_id'])) {
    $whereClauses[] = "p.batch_id = ?";
    $params[] = $_GET['batch_id'];
}

// Construct query with optional filtering
$whereSQL = $whereClauses ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "
    SELECT 
        p.payment_id, s.name AS student_name, c.course_name, b.batch_name, 
        p.payment_type, p.amount, p.payment_date, p.status 
    FROM Payments p
    JOIN Students s ON p.student_id = s.student_id
    JOIN Courses c ON p.course_id = c.course_id
    JOIN Batches b ON p.batch_id = b.batch_id
    $whereSQL
    ORDER BY p.payment_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch active batches for the selected course
if (!empty($_GET['course_id'])) {
    $batchesQuery = "SELECT batch_id, batch_name FROM Batches WHERE course_id = ? AND active = '1'";
    $batchesStmt = $conn->prepare($batchesQuery);
    $batchesStmt->execute([$_GET['course_id']]);
    $batches = $batchesStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If no course selected, fetch all active batches
    $batches = $conn->query("SELECT batch_id, batch_name FROM Batches WHERE active = '1'")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">View Payments</h1>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="course_id" class="form-label">Filter by Course:</label>
            <select name="course_id" id="course_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Courses</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" <?php echo isset($_GET['course_id']) && $_GET['course_id'] == $course['course_id'] ? 'selected' : ''; ?>>
                        <?php echo $course['course_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="batch_id" class="form-label">Filter by Batch:</label>
            <select name="batch_id" id="batch_id" class="form-control">
                <option value="">All Batches</option>
                <?php foreach ($batches as $batch): ?>
                    <option value="<?php echo $batch['batch_id']; ?>" <?php echo isset($_GET['batch_id']) && $_GET['batch_id'] == $batch['batch_id'] ? 'selected' : ''; ?>>
                        <?php echo $batch['batch_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="view_payments.php" class="btn btn-secondary ms-2">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($payments) > 0): ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['payment_id']; ?></td>
                            <td><?php echo $payment['student_name']; ?></td>
                            <td><?php echo $payment['course_name']; ?></td>
                            <td><?php echo $payment['batch_name']; ?></td>
                            <td><?php echo ucfirst($payment['payment_type']); ?></td>
                            <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                            <td>
                                <span class="badge <?php echo $payment['status'] === 'Completed' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $payment['status']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No payments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/super_admin_footer.php'; ?>