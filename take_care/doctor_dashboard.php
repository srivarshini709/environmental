<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Fetch doctor's details
$doctor_query = "SELECT first_name, last_name, email, phone FROM users WHERE id = ? AND role = 'doctor'";
$doctor_stmt = $conn->prepare($doctor_query);
$doctor_stmt->bind_param("i", $_SESSION['user_id']);
$doctor_stmt->execute();
$doctor_result = $doctor_stmt->get_result();
$doctor = $doctor_result->fetch_assoc();

// Fetch appointments for the logged-in doctor
$sql = "SELECT a.id, a.patient_id, a.appointment_date AS date, a.status, p.first_name AS patient_name 
        FROM appointments a
        JOIN users p ON a.patient_id = p.id
        WHERE a.doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Fetch patients for dropdown in the add record form
$patients_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'patient'";
$patients_result = $conn->query($patients_query);

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    $update_query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?" . ($password ? ", password = ?" : "") . " WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    
    if ($password) {
        $update_stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $password, $_SESSION['user_id']);
    } else {
        $update_stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $_SESSION['user_id']);
    }

    if ($update_stmt->execute()) {
        echo "<p>Profile updated successfully.</p>";
    } else {
        echo "<p>Error updating profile: " . $conn->error . "</p>";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - TakeCare Hospital Management System</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
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
            <h2>Doctor Dashboard</h2>
            <a href="doctor_dashboard.php">Dashboard</a>
            <a href="view_patients.php">View Patients</a>
            <a href="clinical_records.php">Manage Clinical Records</a>
            <a href="appointment_details.php">Appointment</a>
            <a href="add_ehr_record.php">Add EHR</a>
            <a href="view_ehr_records.php">EHR record</a>
            <a href="write_prescription.php">Prescription</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Welcome, Dr. <?php echo htmlspecialchars($doctor['last_name']); ?></h1>

            <!-- Profile Update Form -->
            <div class="card">
                <h3>Update Profile</h3>
                <form action="doctor_dashboard.php" method="POST">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($doctor['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($doctor['last_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter new password (leave blank to keep current)">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="update_profile" value="Update Profile">
                    </div>
                </form>
            </div>
            <!-- Add a section for managing EHR records -->
<div class="card">
    <h3>Add EHR Record</h3>
    <form action="add_ehr_record.php" method="POST">
        <div>
            <label for="patient_id">Patient ID:</label>
            <input type="number" id="patient_id" name="patient_id" required>
        </div>
        <div>
            <label for="record_date">Record Date:</label>
            <input type="date" id="record_date" name="record_date" required>
        </div>
        <div>
            <label for="record_type">Record Type:</label>
            <select id="record_type" name="record_type" required>
                <!-- Options populated from ehr_record_types table -->
                <?php
                // Populate record types from the database
                $type_query = "SELECT type_name FROM ehr_record_types";
                $type_result = $conn->query($type_query);
                while ($row = $type_result->fetch_assoc()) {
                    echo "<option value=\"" . htmlspecialchars($row['type_name']) . "\">" . htmlspecialchars($row['type_name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label for="record_details">Record Details:</label>
            <textarea id="record_details" name="record_details" rows="4" required></textarea>
        </div>
        <div>
            <input type="submit" value="Add Record">
        </div>
    </form>
</div>

<div class="card">
    <h3>View Patient EHR Records</h3>
    <form action="view_ehr_records.php" method="GET">
        <div>
            <label for="patient_id">Patient ID:</label>
            <input type="number" id="patient_id" name="patient_id" required>
        </div>
        <div>
            <input type="submit" value="View Records">
        </div>
    </form>
</div>

            <!-- Add this section to your existing doctor dashboard -->
<div class="card">
    <h3>Drug Interaction Alerts</h3>
    <form action="check_drug_interactions.php" method="POST">
        <div>
            <label for="drug_1">Drug 1:</label>
            <input type="text" id="drug_1" name="drug_1" required>
        </div>
        <div>
            <label for="drug_2">Drug 2:</label>
            <input type="text" id="drug_2" name="drug_2" required>
        </div>
        <div>
            <input type="submit" value="Check Interactions">
        </div>
    </form>
</div>


            <!-- Appointments Table -->
            <div class="card">
                <h3>Your Appointments</h3>
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
                            <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                            <td><a href="appointment_details.php?id=<?php echo htmlspecialchars($appointment['id']); ?>">View Details</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
