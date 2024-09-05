<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Fetch some statistics or data as needed
// Example: Number of users, number of appointments
$user_count_query = "SELECT COUNT(*) as user_count FROM users";
$appointment_count_query = "SELECT COUNT(*) as appointment_count FROM appointments";

$user_count_result = $conn->query($user_count_query);
$appointment_count_result = $conn->query($appointment_count_query);

$user_count = $user_count_result->fetch_assoc()['user_count'];
$appointment_count = $appointment_count_result->fetch_assoc()['appointment_count'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #007BFF;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            margin-top: 0;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 15px 0;
        }
        .sidebar a:hover {
            background-color: #0056b3;
            padding: 10px;
            border-radius: 4px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .main-content h1 {
            margin-top: 0;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card h3 {
            margin-top: 0;
        }
        .card button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .card button:hover {
            background-color: #0056b3;
        }
        .logout {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="view_appointments.php">View Appointments</a>
            <a href="manage_records.php">Manage Records</a>
            <a href="generate_reports.php">Generate Reports</a>
            <a href="billing.php">Billing</a>
            <a href="pharmacy.php">Pharmacy</a>
            <a href="view_feedback.php">Feedback</a>
            <a href="contact_us.php">Contact Us</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Welcome, Admin!</h1>
            <div class="card">
                <h3>Overview</h3>
                <p>Total Users: <?php echo $user_count; ?></p>
                <p>Total Appointments: <?php echo $appointment_count; ?></p>
                <button onclick="window.location.href='manage_users.php'">Manage Users</button>
                <button onclick="window.location.href='view_appointments.php'">View Appointments</button>
                <button onclick="window.location.href='generate_reports.php'">Generate Reports</button>
            
            </div>
            <div class="card">
                <h3>Recent Activities</h3>
                <p>Check the latest activities and updates.</p>
                <!-- Recent activities can be dynamically loaded here -->
            </div>
        </div>
    </div>
</body>
</html>
