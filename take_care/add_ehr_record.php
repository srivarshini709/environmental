<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are set
    if (isset($_POST['patient_id']) && isset($_POST['record_date']) && isset($_POST['record_type']) && isset($_POST['record_details'])) {
        $patient_id = $_POST['patient_id'];
        $record_date = $_POST['record_date'];
        $record_type = $_POST['record_type'];
        $record_details = $_POST['record_details'];

        // Validate input
        if (empty($patient_id) || empty($record_date) || empty($record_type) || empty($record_details)) {
            die('All fields are required.');
        }

        // Prepare and execute the query to insert EHR record
        $sql = "INSERT INTO ehr_records (patient_id, record_date, record_type, record_details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("isss", $patient_id, $record_date, $record_type, $record_details);

        // Execute the query
        if ($stmt->execute() === false) {
            die('Execute failed: ' . $stmt->error);
        }

        echo 'EHR record added successfully.';

        // Close statement and connection
        $stmt->close();
    } else {
        die('Required fields are missing.');
    }
} else {
    die('Invalid request method.');
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add EHR Record</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add EHR Record</h1>
    <form action="add_ehr_record.php" method="POST">
    <label for="patient_id">Patient ID:</label>
    <input type="text" id="patient_id" name="patient_id" required>
    
    <label for="record_date">Record Date:</label>
    <input type="date" id="record_date" name="record_date" required>
    
    <label for="record_type">Record Type:</label>
    <input type="text" id="record_type" name="record_type" required>
    
    <label for="record_details">Record Details:</label>
    <textarea id="record_details" name="record_details" required></textarea>
    
    <input type="submit" value="Add Record">
</form>

    
</body>
</html>
