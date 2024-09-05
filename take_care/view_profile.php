<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}

// Get the logged-in patient's ID
$patient_id = $_SESSION['user_id'];

// Fetch patient profile information
$sql = "SELECT first_name, last_name, email, phone, birthdate, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No profile found.";
    exit();
}

$patient = $result->fetch_assoc();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h1 {
            text-align: center;
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .profile-info p {
            margin: 0 0 15px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: #007BFF;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Patient Profile</h1>
        <div class="profile-info">
            <label for="first_name">First Name:</label>
            <p><?php echo htmlspecialchars($patient['first_name']); ?></p>

            <label for="last_name">Last Name:</label>
            <p><?php echo htmlspecialchars($patient['last_name']); ?></p>

            <label for="email">Email:</label>
            <p><?php echo htmlspecialchars($patient['email']); ?></p>

            <label for="phone">Phone:</label>
            <p><?php echo htmlspecialchars($patient['phone']); ?></p>

            <label for="birthdate">Date of Birth:</label>
            <p><?php echo htmlspecialchars($patient['birthdate']); ?></p>

            <label for="address">Address:</label>
            <p><?php echo htmlspecialchars($patient['address']); ?></p>
        </div>
        <div class="back-link">
            <a href="patient_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
