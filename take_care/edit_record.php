<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Fetch record details
if (isset($_GET['record_id'])) {
    $record_id = $_GET['record_id'];

    $sql = "SELECT id, patient_id, doctor_id, record_date, details FROM records WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $record_id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    
    if (!$record) {
        echo "Record not found.";
        exit();
    }
} else {
    echo "No record ID provided.";
    exit();
}

// Handle record update
if (isset($_POST['update_record'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $record_date = $_POST['record_date'];
    $details = $_POST['details'];

    $sql = "UPDATE records SET patient_id = ?, doctor_id = ?, record_date = ?, details = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $patient_id, $doctor_id, $record_date, $details, $record_id);

    if ($stmt->execute()) {
        header("Location: manage_records.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch patients and doctors for dropdowns
$patients_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'patient'";
$doctors_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'doctor'";
$patients_result = $conn->query($patients_query);
$doctors_result = $conn->query($doctors_query);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record - TakeCare Hospital Management System</title>
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
            <a href="contact_us.php">Contact Us</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Edit Record</h1>

            <!-- Edit Record Form -->
            <div class="card">
                <h3>Edit Record</h3>
                <form action="edit_record.php?record_id=<?php echo htmlspecialchars($record_id); ?>" method="POST">
                    <div class="form-group">
                        <label for="patient_id">Patient</label>
                        <select id="patient_id" name="patient_id" required>
                            <?php while ($patient = $patients_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($patient['id']); ?>" <?php echo $record['patient_id'] == $patient['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($patient['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="doctor_id">Doctor</label>
                        <select id="doctor_id" name="doctor_id" required>
                            <?php while ($doctor = $doctors_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($doctor['id']); ?>" <?php echo $record['doctor_id'] == $doctor['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($doctor['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="record_date">Date</label>
                        <input type="date" id="record_date" name="record_date" value="<?php echo htmlspecialchars($record['record_date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="details">Details</label>
                        <textarea id="details" name="details" rows="4" required><?php echo htmlspecialchars($record['details']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="update_record" value="Update Record">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
