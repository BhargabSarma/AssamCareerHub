<?php
include '../../config.php';

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch active batches for the selected course
    $stmt = $conn->prepare("SELECT batch_id, batch_name FROM Batches WHERE course_id = ? AND active = '1'");
    $stmt->execute([$course_id]);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($batches);
}
