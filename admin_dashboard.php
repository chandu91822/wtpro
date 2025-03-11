<?php
session_start();
$conn = new mysqli("localhost", "root", "", "booking_system");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Adding Users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        $message = "User added successfully!";
    } else {
        $error = "Username already exists!";
    }
}

// Handle Removing Users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_user'])) {
    $username = $_POST['remove_username'];
    
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    
    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM users WHERE username='$username'");
        $message = "User removed successfully!";
    } else {
        $error = "User not found!";
    }
}

// Fetch All Users
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        

        <?php 
            if (isset($message)) echo "<p class='success'>$message</p>";
            if (isset($error)) echo "<p class='error'>$error</p>";
        ?>

        <h3>Add User</h3>
        <form method="POST" action="admin_dashboard.php">
            <input type="text" name="username" placeholder="Enter Username" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <select name="role">
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="admin">Admin</option>
            </select><br>
            <input type="submit" name="add_user" value="Add User">
        </form>
        <br><br>
        <h3>Remove User</h3>
        <form method="POST" action="admin_dashboard.php">
            <select name="remove_username" required>
                <option value="">Select User</option>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <option value="<?= $row['username'] ?>"><?= $row['username'] ?> (<?= ucfirst($row['role']) ?>)</option>
                <?php endwhile; ?>
            </select><br>
            <input type="submit" name="remove_user" value="Remove User">
        </form>

        
    </div>
</body>
</html>
