<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$batch_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = $_POST['batch_id'];
    $batch_name = $_POST['batch_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    try {
        $stmt = $conn->prepare("UPDATE Batches SET batch_name = ?, start_date = ?, end_date = ?, updated_at = NOW() WHERE batch_id = ?");
        $stmt->execute([$batch_name, $start_date, $end_date, $batch_id]);
        $_SESSION['success'] = "Batch updated successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating batch: " . $e->getMessage();
    }

    header("Location: manage_batches.php");
    exit;
}

$batch = $conn->prepare("SELECT * FROM Batches WHERE batch_id = ?");
$batch->execute([$batch_id]);
$batch = $batch->fetch(PDO::FETCH_ASSOC);

if (!$batch) {
    $_SESSION['error'] = "Batch not found!";
    header("Location: manage_batches.php");
    exit;
}

include '../partials/header.php';
?>

<div class="container my-4">
    <h1 class="text-center">Edit Batch</h1>
    <form method="POST" action="">
        <input type="hidden" name="batch_id" value="<?php echo $batch['batch_id']; ?>">
        <div class="mb-3">
            <label for="batch_name" class="form-label">Batch Name</label>
            <input type="text" class="form-control" id="batch_name" name="batch_name" value="<?php echo htmlspecialchars($batch['batch_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($batch['start_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($batch['end_date']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>