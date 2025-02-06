<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['super_admin_id'])) {
    header("Location: super_admin_login.php");
    exit;
}

$error = '';
$success = '';

// Get the register details
if (isset($_GET['register_id'])) {
    $register_id = intval($_GET['register_id']);
    $stmt = $conn->prepare("SELECT * FROM Register WHERE register_id = ?");
    $stmt->execute([$register_id]);
    $register = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$register) {
        $error = "Register not found!";
    }
} else {
    header("Location: manage_register.php");
    exit;
}

// Handle register update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $register['password'];

    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Name, Email, and Phone are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE Register SET name = ?, email = ?, phone = ?, password = ? WHERE register_id = ?");
            $stmt->execute([$name, $email, $phone, $password, $register_id]);

            if ($stmt->rowCount() > 0) {
                $success = "Register updated successfully!";
                // Refresh the register details
                $stmt = $conn->prepare("SELECT * FROM Register WHERE register_id = ?");
                $stmt->execute([$register_id]);
                $register = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "No changes were made.";
            }
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/super_admin_header.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Edit Register</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($register): ?>
        <form method="POST" class="card shadow p-4">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name*:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($register['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email*:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($register['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone*:</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($register['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password (Leave empty to keep current password):</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update Register</button>
            <a href="manage_register.php" class="btn btn-secondary">Back</a>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/super_admin_footer.php'; ?>