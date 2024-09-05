<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medication_name = $_POST['medication_name'];
    $reminder_time = $_POST['reminder_time'];
    $patient_id = $_SESSION['user_id'];

    $sql = "INSERT INTO medication_reminders (patient_id, medication_name, reminder_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("sss", $patient_id, $medication_name, $reminder_time);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Reminder added successfully.";
    } else {
        echo "Error adding reminder.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Reminder</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Medication Reminder</h1>
    <form action="add_reminder.php" method="POST">
        <label for="medication_name">Medication Name:</label>
        <input type="text" id="medication_name" name="medication_name" required>
        <label for="reminder_time">Reminder Time:</label>
        <input type="time" id="reminder_time" name="reminder_time" required>
        <input type="submit" value="Add Reminder">
    </form>
</body>
</html>
