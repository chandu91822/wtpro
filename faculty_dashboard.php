<?php
session_start();
$conn = new mysqli("localhost", "root", "", "booking_system");
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Remove Booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = intval($_POST['booking_id']);
    $conn->query("DELETE FROM bookings WHERE id=$id");
}



$result = $conn->query("SELECT * FROM bookings");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            text-align: center;
            margin: 20px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #3498db;
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:hover {
            background: #e0e0e0;
            transition: 0.3s ease-in-out;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .delete-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <h2>Booking List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Year</th>
            <th>Section</th>
            <th>Faculty</th>
            <th>Subject</th>
            <th>Time Slot From</th>
            <th>Time Slot To</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['student_name'] ?></td>
                <td><?= $row['year'] ?></td>
                <td><?= $row['section'] ?></td>
                <td><?= $row['faculty_name'] ?></td>
                <td><?= $row['subject'] ?></td>
                <td><?= $row['time_slot_from'] ?></td>
                <td><?= $row['time_slot_to'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="delete" class="delete-btn">Remove</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>
