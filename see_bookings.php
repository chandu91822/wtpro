<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "booking_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['student'])) {
    header("Location: student_login.php");
    exit();
}

$student_name = $_SESSION['student'];
$bookings = [];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE student_name = ?");
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if (count($bookings) > 0): ?>

        <table border="1">
            <tr>
                <th>Year</th>
                <th>Section</th>
                <th>Faculty</th>
                <th>Subject</th>
                <th>Class</th>
                <th>From</th>
                <th>To</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['year']); ?></td>
                    <td><?php echo htmlspecialchars($booking['section']); ?></td>
                    <td><?php echo htmlspecialchars($booking['faculty_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['subject']); ?></td>
                    <td><?php echo htmlspecialchars($booking['class']); ?></td>
                    <td><?php echo htmlspecialchars($booking['time_slot_from']); ?></td>
                    <td><?php echo htmlspecialchars($booking['time_slot_to']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</body>
</html>
