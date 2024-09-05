<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a receptionist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: login.html");
    exit();
}

// Fetch receptionist profile
$user_id = $_SESSION['user_id'];
$sql_profile = "SELECT first_name, last_name, email, phone FROM users WHERE id = ?";
$stmt_profile = $conn->prepare($sql_profile);
$stmt_profile->bind_param("i", $user_id);
$stmt_profile->execute();
$profile_result = $stmt_profile->get_result();
$receptionist = $profile_result->fetch_assoc();

// Fetch upcoming appointments
$sql_appointments = "SELECT a.id, a.patient_id, a.appointment_date, a.status, p.first_name AS patient_name 
                     FROM appointments a
                     JOIN users p ON a.patient_id = p.id
                     WHERE a.appointment_date >= CURDATE()
                     ORDER BY a.appointment_date";
$appointments_result = $conn->query($sql_appointments);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css">
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
        .logout {
            color: #fff;
            text-decoration: none;
            display: block;
            margin-top: 20px;
            background-color: #dc3545;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .logout:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Receptionist Dashboard</h2>
            <a href="receptionist_dashboard.php">Dashboard</a>
            <a href="appointment_manager.php">Schedule Appointments</a>
            <a href="patients_overview.php">Patient List</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($receptionist['first_name']); ?>!</h1>

            <!-- Profile Section -->
            <div class="card">
                <h3>Your Profile</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($receptionist['first_name'] . ' ' . $receptionist['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($receptionist['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($receptionist['phone']); ?></p>
            </div>

            <!-- Upcoming Appointments Section -->
            <div class="card">
                <h3>Upcoming Appointments</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
