<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Get appointment ID from query string
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch appointment details
$sql = "SELECT a.id, a.patient_id, a.appointment_date, a.status, a.details, p.first_name AS patient_name, p.last_name AS patient_last_name
        FROM appointments a
        JOIN users p ON a.patient_id = p.id
        WHERE a.id = ? AND a.doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
$stmt->execute();
$appointment_result = $stmt->get_result();
$appointment = $appointment_result->fetch_assoc();

if (!$appointment) {
    echo "No appointment found or you do not have permission to view this appointment.";
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - TakeCare Hospital Management System</title>
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
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 800px;
        }
        .card h3 {
            margin-top: 0;
        }
        .card p {
            margin: 10px 0;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h3>Appointment Details</h3>
            <p><strong>Appointment ID:</strong> <?php echo htmlspecialchars($appointment['id']); ?></p>
            <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']) . ' ' . htmlspecialchars($appointment['patient_last_name']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($appointment['status']); ?></p>
            <p><strong>Details:</strong> <?php echo nl2br(htmlspecialchars($appointment['details'])); ?></p>
            <a href="doctor_dashboard.php" class="back-link">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
