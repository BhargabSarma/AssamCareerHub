<?php
session_start();
include '../../config.php';

// Ensure Super Admin is logged in
if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Input validation
    if (empty($name) || empty($email) || empty($phone) || empty($_POST['password'])) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM Register WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = 'Email is already in use.';
        } else {
            // Insert register into database
            $stmt = $conn->prepare("INSERT INTO Register (name, email, phone, password, active) VALUES (?, ?, ?, ?, 1)");
            if ($stmt->execute([$name, $email, $phone, $password])) {
                $success = 'Register added successfully!';
            } else {
                $error = 'Failed to add Register. Please try again.';
            }
        }
    }
}
?>

<?php include '../../partials/header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Add New Register</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="card shadow p-4">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone:</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Add Register</button>
        <a href="manage_register.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include '../../partials/footer.php'; ?>