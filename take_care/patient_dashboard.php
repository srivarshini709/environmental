<?php include 'chatbot.php'; ?>

<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}

// Fetch patient details
$patient_id = $_SESSION['user_id'];
$patient_query = "SELECT first_name, last_name, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($patient_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient_result = $stmt->get_result();
$patient = $patient_result->fetch_assoc();

// Fetch patient appointments
$appointments_query = "SELECT a.id, a.appointment_date, a.status, d.first_name AS doctor_name 
                       FROM appointments a
                       JOIN users d ON a.doctor_id = d.id
                       WHERE a.patient_id = ?";
$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Fetch patient medical records
$records_query = "SELECT id, details, date_of_record FROM patient_records WHERE patient_id = ?";
$stmt = $conn->prepare($records_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$records_result = $stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        /* Add your CSS styles here */
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
            <h2>Patient Dashboard</h2>
            <a href="patient_dashboard.php">Dashboard</a>
            <a href="view_profile.php">View Profile</a>
            
            <a href="track_adherence.php">Track Adherence</a>
            <a href="appointment.php">View Appointments</a>
            <a href="automated_refills.php">Automated Refills</a>
            <a href="view_medical_records.php">View Medical Records</a>
            <a href="feedback.php">Feedback</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($patient['first_name']); ?> <?php echo htmlspecialchars($patient['last_name']); ?></h1>

            <!-- Profile Section -->
            <div class="card">
                <h3>Your Profile</h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
            </div>

            <!-- Appointments Section -->
            <div class="card">
                <h3>Your Appointments</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Doctor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Add this section to your existing patient dashboard -->
<div class="card">
    <h3>Track Medication Adherence</h3>
    <form action="track_adherence.php" method="POST">
        <div>
            <label for="medication_name">Medication Name:</label>
            <input type="text" id="medication_name" name="medication_name" required>
        </div>
        <div>
            <label for="adherence_status">Adherence Status:</label>
            <select id="adherence_status" name="adherence_status" required>
                <option value="Adhered">Adhered</option>
                <option value="Not Adhered">Not Adhered</option>
            </select>
        </div>
        <div>
            <input type="submit" value="Submit">
        </div>
    </form>
</div>



            <!-- Medical Records Section -->
            <div class="card">
                <h3>Your Medical Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Details</th>
                            <th>Date of Record</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($record = $records_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo htmlspecialchars($record['details']); ?></td>
                            <td><?php echo htmlspecialchars($record['date_of_record']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Add this section to your existing patient dashboard -->


    </div>
</body>
</html>
