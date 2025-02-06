<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['register_id'])) {
    header("Location: register_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : rand(10000, 99999); // Auto-generate 5-digit password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $custom_address = !empty($_POST['custom_address']) ? $_POST['custom_address'] : null;
    $address = $custom_address ?: "$city, $state";
    $course_id = $_POST['course_id'];
    $batch_id = $_POST['batch_id'];

    try {
        // Insert student into Students table
        $stmt = $conn->prepare("INSERT INTO Students (name, email, password, phone, gender, state, city, address) VALUES (?, ?, ?, ?, ?,?,?,?)");
        $stmt->execute([$name, $email, $hashed_password, $phone, $gender, $state, $city, $address]);

        // Get the last inserted student ID
        $student_id = $conn->lastInsertId();

        // Allocate student to course and batch
        if (!empty($course_id) && !empty($batch_id)) {
            $stmt = $conn->prepare("INSERT INTO Student_Batches (student_id, batch_id) VALUES (?, ?)");
            $stmt->execute([$student_id, $batch_id]);
        }

        $_SESSION['success'] = "Student added successfully! Password: $password"; // Show generated password
        header("Location: manage_students.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Fetch active courses
$coursesStmt = $conn->prepare("SELECT course_id, course_name FROM Courses WHERE active = '1'");
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Add New Student</h1>
    <form method="POST" id="add-student-form">
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



        <div class="form-group">
            <label for="course_id">Select Course</label>
            <select id="course_id" name="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
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

        <button type="submit" class="btn btn-primary mt-3">Add Student</button>
    </form>
</div>

<?php include '../footer.php'; ?>
<script src="../../js/cities.js"></script>

<script>
    // Password Generator
    document.getElementById('generate-password').addEventListener('click', function() {
        document.getElementById('password').value = Math.floor(10000 + Math.random() * 90000); // Generate 5-digit password
    });

    document.addEventListener("DOMContentLoaded", function() {
        const stateSelect = document.getElementById("state");
        const citySelect = document.getElementById("city");

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
    });
    document.addEventListener("DOMContentLoaded", function() {
        const customAddressToggle = document.getElementById("custom_address_toggle");
        const customAddressInput = document.getElementById("custom_address");
        const stateSelect = document.getElementById("state");
        const citySelect = document.getElementById("city");

        // Disable the custom address input by default
        customAddressInput.disabled = true;

        // Toggle custom address input based on checkbox
        customAddressToggle.addEventListener("change", function() {
            if (this.checked) {
                customAddressInput.disabled = false;
                customAddressInput.focus(); // Focus on the input when enabled
            } else {
                customAddressInput.disabled = true;
                customAddressInput.value = ""; // Clear the input when unchecked
            }
        });

        // Auto-fill the address if custom address is not used
        function updateAddress() {
            if (!customAddressToggle.checked) {
                customAddressInput.value = `${citySelect.value}, ${stateSelect.value}`.trim();
            }
        }

        // Update address when state or city is changed
        stateSelect.addEventListener("change", updateAddress);
        citySelect.addEventListener("change", updateAddress);
    });

    // Course Selection -> Load Active Batches
    document.getElementById('course_id').addEventListener('change', function() {
        const selectedCourse = this.value;
        const batchContainer = document.getElementById('batch-container');
        const batchSelect = document.getElementById('batch_id');

        batchSelect.innerHTML = '<option value="">-- Select Batch --</option>';

        if (selectedCourse) {
            batchContainer.style.display = 'block';

            // Fetch batches based on the selected course
            fetch(`get_batches.php?course_id=${selectedCourse}`)
                .then(response => response.json())
                .then(batches => {
                    batchSelect.innerHTML = '<option value="">-- Select Batch --</option>'; // Reset dropdown

                    batches.forEach(batch => {
                        const option = document.createElement('option');
                        option.value = batch.batch_id;
                        option.textContent = batch.batch_name;
                        batchSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching batches:', error));
        } else {
            batchContainer.style.display = 'none';
        }
    });
</script>