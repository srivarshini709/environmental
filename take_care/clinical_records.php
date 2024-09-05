<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Handle record addition
if (isset($_POST['add_record'])) {
    $patient_id = $_POST['patient_id'];
    $record_date = $_POST['record_date'];
    $details = $_POST['details'];

    $sql = "INSERT INTO clinical_records (patient_id, doctor_id, record_date, details) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $patient_id, $_SESSION['user_id'], $record_date, $details);

    if ($stmt->execute()) {
        echo "Record added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle record update
if (isset($_POST['update_record'])) {
    $record_id = $_POST['record_id'];
    $patient_id = $_POST['patient_id'];
    $record_date = $_POST['record_date'];
    $details = $_POST['details'];

    $sql = "UPDATE clinical_records SET patient_id = ?, record_date = ?, details = ? WHERE id = ? AND doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $patient_id, $record_date, $details, $record_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo "Record updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle record deletion
if (isset($_GET['delete_record'])) {
    $record_id = $_GET['delete_record'];

    $sql = "DELETE FROM clinical_records WHERE id = ? AND doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $record_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo "Record deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch clinical records
$sql = "SELECT r.id, r.patient_id, r.record_date, r.details, p.first_name AS patient_name 
        FROM clinical_records r
        JOIN users p ON r.patient_id = p.id
        WHERE r.doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Fetch patients for dropdown
$patients_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'patient'";
$patients_result = $conn->query($patients_query);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Records - TakeCare Hospital Management System</title>
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
        .actions button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .actions button:hover {
            background-color: #0056b3;
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
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Clinical Records</h1>

            <!-- Add Record Form -->
            <div class="card">
                <h3>Add New Record</h3>
                <form action="clinical_records.php" method="POST">
                    <div class="form-group">
                        <label for="patient_id">Patient</label>
                        <select id="patient_id" name="patient_id" required>
                            <?php while ($patient = $patients_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($patient['id']); ?>"><?php echo htmlspecialchars($patient['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="record_date">Date</label>
                        <input type="date" id="record_date" name="record_date" required>
                    </div>
                    <div class="form-group">
                        <label for="details">Details</label>
                        <textarea id="details" name="details" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_record" value="Add Record">
                    </div>
                </form>
            </div>

            <!-- Records Table -->
            <div class="card">
                <h3>Clinical Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($record = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                            <td><?php echo htmlspecialchars($record['details']); ?></td>
                            <td class="actions">
                                <a href="edit_clinical_record.php?record_id=<?php echo htmlspecialchars($record['id']); ?>">Edit</a> | 
                                <a href="clinical_records.php?delete_record=<?php echo htmlspecialchars($record['id']); ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
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
