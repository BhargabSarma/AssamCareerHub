<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    // Check if username or email already exists
    $check = $conn->prepare("SELECT * FROM Admins WHERE username = :username OR email = :email");
    $check->execute(['username' => $username, 'email' => $email]);
    if ($check->rowCount() > 0) {
        $error = "Username or email already exists.";
    } else {
        // Insert new admin
        $stmt = $conn->prepare("INSERT INTO Admins (username, password, email) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $password, $email])) {
            header("Location: admin_login.php");
            exit;
        } else {
            $error = "Failed to register. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Registration</title>
</head>

<body>
    <h2>Admin Registration</h2>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Register</button>
    </form>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
</body>

</html>