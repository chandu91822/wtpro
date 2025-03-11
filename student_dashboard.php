<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "booking_system");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure student is logged in
if (!isset($_SESSION['student'])) {
    header("Location: student_login.php");
    exit();
}

$student_name = $_SESSION['student'];
$bookings = [];

// Fetch student bookings securely
$stmt = $conn->prepare("SELECT year, section, faculty_name, subject, class, time_slot_from, time_slot_to FROM bookings WHERE student_name = ?");
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_class'])) {
    // Sanitize input
    $year = intval($_POST['year']);
    $section = htmlspecialchars($_POST['section']);
    $faculty_name = htmlspecialchars($_POST['faculty_name']);
    $subject = htmlspecialchars($_POST['subject']);
    $class = htmlspecialchars($_POST['class']);
    $time_slot_from = $_POST['time_slot_from'];
    $time_slot_to = $_POST['time_slot_to'];

    $start_time = strtotime($time_slot_from);
    $end_time = strtotime($time_slot_to);

    if ($end_time <= $start_time) {
        $error = "End time must be after start time!";
    } else {
        // Check if the class is already booked within the time range
        $stmt = $conn->prepare(
            "SELECT COUNT(*) FROM bookings 
            WHERE class = ? 
            AND (
                (time_slot_from <= ? AND time_slot_to > ?) OR 
                (time_slot_from < ? AND time_slot_to >= ?) OR 
                (time_slot_from >= ? AND time_slot_to <= ?)
            )"
        );
        $stmt->bind_param("sssssss", $class, $time_slot_from, $time_slot_from, $time_slot_to, $time_slot_to, $time_slot_from, $time_slot_to);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            // Insert booking if no conflicts
            $stmt = $conn->prepare(
                "INSERT INTO bookings (student_name, year, section, faculty_name, subject, class, time_slot_from, time_slot_to) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sissssss", $student_name, $year, $section, $faculty_name, $subject, $class, $time_slot_from, $time_slot_to);

            if ($stmt->execute()) {
                $message = "Class booked successfully!";
                header("Location: student_dashboard.php"); // Refresh page safely
                exit();
            } else {
                $error = "Error booking class: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Time slot already booked for this class!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleSection(section) {
            document.getElementById('book-class').style.display = (section === 'book') ? 'block' : 'none';
            document.getElementById('see-classes').style.display = (section === 'see') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>
        <div class="role-selection">
            <a href="see_bookings.php"><button type="button">See My Bookings</button></a>
            <a href="book_class.php"><button type="button">Book a Class</button></a>
        </div>   
    </div>
</body>
</html>
