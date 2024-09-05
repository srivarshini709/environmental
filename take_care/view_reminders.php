<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}

$patient_id = $_SESSION['user_id']; // Get patient ID from session

// Prepare and execute the query to fetch medication reminders for the logged-in patient
$sql = "SELECT * FROM medication_reminders WHERE patient_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$reminders_result = $stmt->get_result();

if ($reminders_result === false) {
    die('Execute failed: ' . $conn->error);
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Medication Reminders</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Your Medication Reminders</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Medication Name</th>
                <th>Reminder Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($reminder = $reminders_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($reminder['id']); ?></td>
                <td><?php echo htmlspecialchars($reminder['medication_name']); ?></td>
                <td><?php echo htmlspecialchars($reminder['reminder_time']); ?></td>
                <td><?php echo htmlspecialchars($reminder['status']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
<script src="reminder_notifications.js"></script>

</html>
