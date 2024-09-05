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
    $refill_date = $_POST['refill_date'];
    $patient_id = $_SESSION['user_id'];

    $sql = "INSERT INTO automated_refills (patient_id, medication_name, refill_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("sss", $patient_id, $medication_name, $refill_date);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Automated refill scheduled successfully.";
    } else {
        echo "Error scheduling automated refill.";
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
    <title>Automated Refill</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Schedule Automated Refill</h1>
    <form action="automated_refills.php" method="POST">
        <label for="medication_name">Medication Name:</label>
        <input type="text" id="medication_name" name="medication_name" required>
        <label for="refill_date">Refill Date:</label>
        <input type="date" id="refill_date" name="refill_date" required>
        <input type="submit" value="Schedule Refill">
    </form>
</body>
</html>
