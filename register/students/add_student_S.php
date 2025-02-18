<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : rand(10000, 99999);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $custom_address = !empty($_POST['custom_address']) ? $_POST['custom_address'] : null;
    $address = $custom_address ?: "$city, $state";
    $course_id = $_POST['course_id'];
    $batch_id = $_POST['batch_id'];

    // Fetch course details (course fee, booking amount)
    $stmt = $conn->prepare("SELECT fee, booking_amount FROM Courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        $_SESSION['error'] = "Course not found!";
        header("Location: add_student.php");
        exit;
    }

    $booking_amount = $course['booking_amount'];
    $total_fee = $course['fee'];

    // Payment calculation
    $payment_option = $_POST['payment_option']; // Get selected radio button value
    // Collect payment type
    $payment_type = $_POST['payment_type']; // Get selected payment type (Online, Offline, or Bank Transfer)
    $installment_1 = 0;
    $installment_2 = 0;
    $full_payment = 0;
    $paid_amount = 0;
    $status = 'Pending';

    if ($payment_option == "no_payment") {
        $paid_amount = $booking_amount;
    } elseif ($payment_option == "first_installment") {
        $installment_1 = ($total_fee - $booking_amount) / 2;
        $paid_amount = $booking_amount + $installment_1;
    } elseif ($payment_option == "full_payment") {
        $full_payment = $total_fee - $booking_amount;
        $paid_amount = $total_fee;
    }

    // Set status
    if ($paid_amount == $total_fee) {
        $status = 'Paid';
    } else {
        $status = 'Partially Paid';
    }

    try {
        // Insert student data
        $stmt = $conn->prepare("INSERT INTO Students (name, email, password, phone, gender, state, city, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $phone, $gender, $state, $city, $address]);
        $student_id = $conn->lastInsertId();

        // Allocate student to batch
        $stmt = $conn->prepare("INSERT INTO student_batches (student_id, batch_id, registration_status, payment_status) 
                        VALUES (?, ?, 'Booked', 'Pending')");
        $stmt->execute([$student_id, $batch_id]);

        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO Payments 
(student_id, course_id, booking_amount, installment_1, installment_2, full_payment, status, payment_date, payment_type, amount, batch_id) 
VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)");
        $stmt->execute([$student_id, $course_id, $booking_amount, $installment_1, $installment_2, $full_payment, $status, $payment_type, $paid_amount, $batch_id]);

        // After inserting the student and payment
        $_SESSION['success'] = "Student added successfully!";
        header("Location: ../payments/manage_payments.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Fetch active courses
$coursesStmt = $conn->prepare("SELECT course_id, course_name, fee FROM Courses WHERE active = '1'");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Add New Student</h1>
    <form method="POST" id="add-student-form">
        <!-- Form fields for student details -->
        <div class="form-group">
            <label for="name">Student Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password (Auto-generated or custom entry)</label>
            <div class="input-group">
                <input type="text" id="password" name="password" class="form-control" readonly>
                <button type="button" id="generate-password" class="btn btn-secondary">Generate</button>
            </div>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control">
        </div>

        <div class="form-group">
            <label>Gender</label><br>
            <input type="radio" id="male" name="gender" value="Male" required> <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="Female" required> <label for="female">Female</label>
            <input type="radio" id="other" name="gender" value="Other" required> <label for="other">Other</label>
        </div>

        <div class="form-group">
            <label for="state">State</label>
            <select id="state" name="state" class="form-control" required>
                <option value="">-- Select State --</option>
            </select>
        </div>

        <div class="form-group">
            <label for="city">City</label>
            <select id="city" name="city" class="form-control" required>
                <option value="">-- Select City --</option>
            </select>
        </div>

        <div class="form-group">
            <input type="checkbox" id="custom_address_toggle">
            <label for="custom_address_toggle">Enter custom address</label>
        </div>

        <div class="form-group">
            <label for="custom_address">Custom Address</label>
            <input type="text" id="custom_address" name="custom_address" class="form-control" disabled>
        </div>

        <!-- Course selection and payment -->
        <div class="form-group">
            <label for="course_id">Select Course</label>
            <select id="course_id" name="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" data-fee="<?php echo $course['fee']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Batches dropdown based on course selection -->
        <div class="form-group" id="batch-container" style="display:none;">
            <label for="batch_id">Select Batch</label>
            <select id="batch_id" name="batch_id" class="form-control" required>
                <option value="">-- Select Batch --</option>
            </select>
        </div>

        <!-- Payment Section -->
        <div id="payment-section" style="display:none;">
            <h4>Payment Details</h4>
            <div class="form-group">
                <label for="booking_amount">Booking Amount</label>
                <input type="text" id="booking_amount" name="booking_amount" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Payment Option</label><br>
                <input type="radio" id="no_payment" name="payment_option" value="no_payment" checked>
                <label for="no_payment">No Payment</label>

                <input type="radio" id="first_installment" name="payment_option" value="first_installment">
                <label for="first_installment">First Installment</label>

                <input type="radio" id="full_payment" name="payment_option" value="full_payment">
                <label for="full_payment">Full Payment</label>
            </div>

            <div class="form-group">
                <label for="installment_1">1st Installment</label>
                <input type="text" id="installment_1" name="installment_1" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="installment_2">2nd Installment</label>
                <input type="text" id="installment_2" name="installment_2" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="remaining_fee">Remaining Fee</label>
                <input type="text" id="remaining_fee" name="remaining_fee" class="form-control" readonly>
            </div>
        </div>
        <div class="form-group">
            <label>Payment Type</label><br>
            <input type="radio" id="payment_online" name="payment_type" value="Online" checked>
            <label for="payment_online">Online</label>

            <input type="radio" id="payment_offline" name="payment_type" value="Offline">
            <label for="payment_offline">Offline</label>

            <input type="radio" id="payment_bank_transfer" name="payment_type" value="Bank Transfer">
            <label for="payment_bank_transfer">Bank Transfer</label>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Add Student</button>
    </form>
</div>

<?php include '../footer.php'; ?>
<script src="../../js/cities.js"></script>
<script src="../../js/add_student.js"></script>

<!-- <script>
    // Password Generator
    document.getElementById('generate-password').addEventListener('click', function() {
        document.getElementById('password').value = Math.floor(10000 + Math.random() * 90000); // Generate 5-digit password
    });

    document.addEventListener("DOMContentLoaded", function() {
        const stateSelect = document.getElementById("state");
        const citySelect = document.getElementById("city");
        const customAddressToggle = document.getElementById("custom_address_toggle");
        const customAddressInput = document.getElementById("custom_address");

        // Populate states
        Object.keys(citiesByState).forEach(state => {
            const option = document.createElement("option");
            option.value = state;
            option.textContent = state;
            stateSelect.appendChild(option);
        });

        // Handle state change
        stateSelect.addEventListener("change", function() {
            const selectedState = this.value;
            citySelect.innerHTML = '<option value="">-- Select City --</option>'; // Reset city dropdown

            if (selectedState && citiesByState[selectedState]) {
                citiesByState[selectedState].forEach(city => {
                    const option = document.createElement("option");
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
            }
        });

        // Handle custom address toggle
        customAddressToggle.addEventListener("change", function() {
            if (this.checked) {
                customAddressInput.disabled = false;
                customAddressInput.required = true;
            } else {
                customAddressInput.disabled = true;
                customAddressInput.required = false;
                customAddressInput.value = ''; // Reset field when unchecked
            }
        });
    });

    document.getElementById('course_id').addEventListener('change', function() {
        const selectedCourse = this.value;
        const batchContainer = document.getElementById('batch-container');
        const paymentSection = document.getElementById('payment-section');
        const courseOption = this.options[this.selectedIndex];
        const bookingAmountInput = document.getElementById('booking_amount');
        const remainingFeeInput = document.getElementById('remaining_fee');
        const installment1Input = document.getElementById('installment_1');
        const installment2Input = document.getElementById('installment_2');

        batchContainer.style.display = 'none';
        paymentSection.style.display = 'none';

        if (selectedCourse) {
            // Fetch course details (booking amount)
            fetch(`get_course_details.php?course_id=${selectedCourse}`)
                .then(response => response.json())
                .then(course => {
                    if (course) {
                        const totalFee = parseFloat(course.fee);
                        const bookingAmount = parseFloat(course.booking_amount);

                        bookingAmountInput.value = bookingAmount;
                        remainingFeeInput.value = totalFee - bookingAmount;

                        // Calculate installment amounts
                        const remainingFee = totalFee - bookingAmount;
                        const installmentAmount = remainingFee / 2;
                        installment1Input.value = installmentAmount;
                        installment2Input.value = installmentAmount;

                        // Show payment section
                        paymentSection.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error fetching course details:', error));

            // Fetch batches based on the selected course
            fetch(`get_batches.php?course_id=${selectedCourse}`)
                .then(response => response.json())
                .then(batches => {
                    const batchSelect = document.getElementById('batch_id');
                    batchSelect.innerHTML = '<option value="">-- Select Batch --</option>'; // Reset dropdown

                    batches.forEach(batch => {
                        const option = document.createElement('option');
                        option.value = batch.batch_id;
                        option.textContent = batch.batch_name;
                        batchSelect.appendChild(option);
                    });

                    // Show batch selection
                    batchContainer.style.display = 'block';
                })
                .catch(error => console.error('Error fetching batches:', error));
        }
    });

    // Handle Payment Option Selection
    document.querySelectorAll('input[name="payment_option"]').forEach(option => {
        option.addEventListener('change', function() {
            const bookingAmount = parseFloat(document.getElementById('booking_amount').value) || 0;
            const remainingFeeInput = document.getElementById('remaining_fee');
            const installment1Input = document.getElementById('installment_1');
            const installment2Input = document.getElementById('installment_2');
            const courseOption = document.getElementById('course_id').options[document.getElementById('course_id').selectedIndex];
            const courseFee = parseFloat(courseOption.getAttribute('data-fee')) || 0;

            // Calculate the correct remaining fee
            const remainingFee = courseFee - bookingAmount;

            if (this.value === "no_payment") {
                installment1Input.value = (remainingFee / 2).toFixed(2);
                installment2Input.value = (remainingFee / 2).toFixed(2);
                remainingFeeInput.value = remainingFee.toFixed(2);
            } else if (this.value === "first_installment") {
                installment1Input.value = (remainingFee / 2).toFixed(2);
                installment2Input.value = 0;
                remainingFeeInput.value = installment1Input.value;
            } else if (this.value === "full_payment") {
                installment1Input.value = remainingFee.toFixed(2);
                installment2Input.value = 0;
                remainingFeeInput.value = 0;
            }
        });
    });
</script> -->