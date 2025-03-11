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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_class'])) {
    $year = intval($_POST['year']);
    $section = $conn->real_escape_string($_POST['section']);
    $faculty_name = $conn->real_escape_string($_POST['faculty_name']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $class = $conn->real_escape_string($_POST['class']);
    $time_slot_from = $conn->real_escape_string($_POST['time_slot_from']);
    $time_slot_to = $conn->real_escape_string($_POST['time_slot_to']);

    $start_time = strtotime($time_slot_from);
    $end_time = strtotime($time_slot_to);

    if ($end_time <= $start_time) {
        $error = "End time must be after start time!";
    } else {
        $stmt = $conn->prepare(
            "SELECT * FROM bookings 
            WHERE class = ? 
            AND (
                (time_slot_from <= ? AND time_slot_to > ?) OR 
                (time_slot_from < ? AND time_slot_to >= ?) OR 
                (time_slot_from >= ? AND time_slot_to <= ?)
            )"
        );
        $stmt->bind_param("sssssss", $class, $time_slot_from, $time_slot_from, $time_slot_to, $time_slot_to, $time_slot_from, $time_slot_to);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt = $conn->prepare(
                "INSERT INTO bookings (student_name, year, section, faculty_name, subject, class, time_slot_from, time_slot_to) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sissssss", $student_name, $year, $section, $faculty_name, $subject, $class, $time_slot_from, $time_slot_to);

            if ($stmt->execute()) {
                $message = "Class booked successfully!";
            } else {
                $error = "Error booking class: " . $stmt->error;
            }
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
    <title>Book a Class</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1> Book a class</h1>
        <?php 
        if (isset($message)) echo "<p class='success'>$message</p>";
        if (isset($error)) echo "<p class='error'>$error</p>";
        ?>
        <form method="POST" action="book_class.php">
    
        <input type="hidden" name="book_class" value="1">
        <input type="number" name="year" placeholder="Year (e.g., 1, 2, 3, 4)" required><br>
        <input type="text" name="section" placeholder="Section" required><br>
        <input type="text" name="faculty_name" placeholder="Faculty Name" required><br>
        <input type="text" name="subject" placeholder="Subject" required><br>
        Class:<br>
        <select name="class" required>
            <option value="GF-1">GF-1</option>
            <option value="GF-2">GF-2</option>
            <option value="GF-3">GF-3</option>
            <option value="GF-4">GF-4</option>
            <option value="GF-5">GF-5</option>
            <option value="GF-6">GF-6</option>
            <option value="GF-7">GF-7</option>
            <option value="GF-8">GF-8</option>
            <option value="GF-9">GF-9</option>
            <option value="GF-10">GF-10</option>
            <option value="Small seminar hall">Small seminar hall</option>
            <option value="Big seminar hall">Big seminar hall</option>
        </select><br>
        Time slot:<br><br>From:<br><input type="datetime-local" name="time_slot_from" required><br>
        <br>To:<br><input type="datetime-local" name="time_slot_to" required><br>
        <input type="submit" value="Book Class">
    </form>
    </div>
    
</body>
</html>
