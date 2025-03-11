<?php
session_start();
header('Content-Type: application/json'); // Ensures a proper response format

$conn = new mysqli("localhost", "root", "", "booking_system");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if user is logged in
if (!isset($_SESSION['student'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$student_name = $_SESSION['student']; // Ensure only the logged-in student can cancel their own bookings

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Ensure student can only delete their own booking
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND student_name = ?");
    $stmt->bind_param("is", $booking_id, $student_name);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to cancel booking."]);
    }
}
?>
