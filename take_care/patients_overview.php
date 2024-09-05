<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.html");
    exit();
}

// Fetch patients
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, email, phone FROM users WHERE role = 'patient'";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Overview - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Staff Dashboard</h2>
            <a href="staff_dashboard.php">Dashboard</a>
            <a href="patients_overview.php">Patients Overview</a>
            <a href="appointment_manager.php">Manage Appointments</a>
            <a href="pharmacy_dashboard.php">Pharmacy Dashboard</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Patients Overview</h1>

            <!-- Patients Table -->
            <div class="card">
                <h3>Patients</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['id']); ?></td>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                            <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                            <td class="actions">
                                <a href="view_patient.php?patient_id=<?php echo htmlspecialchars($patient['id']); ?>">View Details</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
