<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.html");
    exit();
}

// Fetch patient records
$sql = "SELECT r.id, r.patient_id, r.date_of_record, r.details, p.first_name, p.last_name
        FROM patient_records r
        JOIN users p ON r.patient_id = p.id";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records - TakeCare Hospital Management System</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Staff Dashboard</h2>
            <a href="staff_dashboard.php">Dashboard</a>
            <a href="patient_list.php">View Patients</a>
            <a href="appointment_schedule.php">Appointment Schedule</a>
            <a href="patient_records.php">Patient Records</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Patient Records</h1>

            <!-- Patient Records Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($record = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['id']); ?></td>
                        <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                        <td><?php echo htmlspecialchars($record['details']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
