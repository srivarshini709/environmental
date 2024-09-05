<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: login.html");
    exit();
}

// Fetch nurse details
$nurse_id = $_SESSION['user_id'];
$nurse_query = "SELECT * FROM users WHERE id = ? AND role = 'nurse'";
$stmt = $conn->prepare($nurse_query);
$stmt->bind_param("i", $nurse_id);
$stmt->execute();
$nurse_result = $stmt->get_result();
$nurse = $nurse_result->fetch_assoc();

// Fetch patients
$patients_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, phone FROM users WHERE role = 'patient'";
$patients_result = $conn->query($patients_query);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Patients - TakeCare Hospital Management System</title>
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
            <h2>Nurse Dashboard</h2>
            <a href="nurse_dashboard.php">Dashboard</a>
            <a href="nurse_patient_records.php">Patient Records</a>
            <a href="nurse_appointments.php">Appointments</a>
            <a href="nurse_patients.php">Patients</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Patients</h1>

            <!-- Patients Table -->
            <div class="card">
                <h3>Patient List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = $patients_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['id']); ?></td>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                            <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
