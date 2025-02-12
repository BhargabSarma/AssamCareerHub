<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

if (isset($_GET['student_id']) && isset($_GET['course_id'])) {
    $student_id = $_GET['student_id'];
    $course_id = $_GET['course_id'];

    // Fetch student and course details
    $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT course_name, fee, booking_amount FROM Courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch the student's payment records
    $stmt = $conn->prepare("SELECT * FROM Payments WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$student_id, $course_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$student || !$course) {
        $_SESSION['error'] = "Invalid student or course!";
        header("Location: manage_students.php");
        exit;
    }

    // Calculate the total payment and remaining balance
    $total_fee = $course['fee'];
    $booking_amount = $course['booking_amount'];
    $installment_1 = ($total_fee - $booking_amount) / 2;
    $installment_2 = ($total_fee - $booking_amount) / 2;
    $remaining_fee = $total_fee - $booking_amount - array_sum(array_column($payments, 'amount'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Process payment
        $payment_amount = $_POST['payment_amount'];
        $payment_type = $_POST['payment_type'];
        $payment_status = $_POST['payment_status'];

        // Insert the payment record
        try {
            $stmt = $conn->prepare("INSERT INTO Payments (student_id, course_id, amount, payment_type, payment_status, payment_date) 
                                    VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$student_id, $course_id, $payment_amount, $payment_type, $payment_status]);

            $_SESSION['success'] = "Payment recorded successfully!";
            header("Location: manage_payments.php?student_id=$student_id&course_id=$course_id");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: manage_students.php");
    exit;
}
?>

<?php include '../header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Manage Payments for Student: <?php echo htmlspecialchars($student['name']); ?></h1>

    <!-- Display Course and Payment Information -->
    <h3>Course: <?php echo htmlspecialchars($course['course_name']); ?></h3>
    <p>Fee: ₹<?php echo number_format($total_fee, 2); ?></p>
    <p>Booking Amount: ₹<?php echo number_format($booking_amount, 2); ?></p>
    <p>Remaining Fee: ₹<?php echo number_format($remaining_fee, 2); ?></p>

    <!-- Display Existing Payment History -->
    <h4>Payment History</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo date("d-m-Y", strtotime($payment['payment_date'])); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_type']); ?></td>
                    <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Form to Record New Payment -->
    <h4>Record New Payment</h4>
    <form method="POST">
        <div class="form-group">
            <label for="payment_amount">Payment Amount</label>
            <input type="number" id="payment_amount" name="payment_amount" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="payment_type">Payment Type</label>
            <select id="payment_type" name="payment_type" class="form-control" required>
                <option value="Cash">Cash</option>
                <option value="Cheque">Cheque</option>
                <option value="Online">Online</option>
            </select>
        </div>

        <div class="form-group">
            <label for="payment_status">Payment Status</label>
            <select id="payment_status" name="payment_status" class="form-control" required>
                <option value="Paid">Paid</option>
                <option value="Pending">Pending</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Record Payment</button>
    </form>
</div>

<?php include '../footer.php'; ?>