<?php
include '../../config.php';

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details (total fee & booking amount)
    $stmt = $conn->prepare("SELECT fee, booking_amount FROM Courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course) {
        echo json_encode($course);
    } else {
        echo json_encode(["error" => "Course not found"]);
    }
}
