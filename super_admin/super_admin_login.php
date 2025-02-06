<?php
session_start();
include '../config.php';

// // Hash the password
// $password = password_hash('superadmin123', PASSWORD_DEFAULT);

// try {
//     $stmt = $conn->prepare("INSERT INTO Super_Admins (username, email, password) VALUES (?, ?, ?)");
//     $stmt->execute(['superadmin', 'superadmin@example.com', $password]);
//     echo "Super Admin created successfully.";
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }


if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM Super_Admins WHERE email = ?");
    $stmt->execute([$email]);
    $super_admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($super_admin && password_verify($password, $super_admin['password'])) {
        $_SESSION['super_admin_id'] = $super_admin['super_admin_id'];
        header("Location: super_admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assam Career Hub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand">Assam Career Hub Super Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-center mb-4">Super Admin Login</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="super_admin_login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
        </form>
    </div>

    <?php include './includes/super_admin_footer.php'; ?>