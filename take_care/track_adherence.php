<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields are set
    if (isset($_POST['medication_name']) && isset($_POST['adherence_status'])) {
        $medication_name = $_POST['medication_name'];
        $adherence_status = $_POST['adherence_status'];

        // Validate input
        if (empty($medication_name) || empty($adherence_status)) {
            die('Medication name and adherence status are required.');
        }

        // Prepare and execute the query to insert adherence record
        $sql = "INSERT INTO adherence_records (medication_name, adherence_status, patient_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        // Bind parameters
        $patient_id = $_SESSION['user_id'];
        $stmt->bind_param("ssi", $medication_name, $adherence_status, $patient_id);

        // Execute the query
        if ($stmt->execute() === false) {
            die('Execute failed: ' . $stmt->error);
        }

        echo 'Adherence record added successfully.';

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
    <title>Track Medication Adherence</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Track Medication Adherence</h1>
    <form action="track_adherence.php" method="POST">
    <label for="medication_name">Medication Name:</label>
    <input type="text" id="medication_name" name="medication_name" required>
    <label for="adherence_status">Adherence Status:</label>
    <select id="adherence_status" name="adherence_status" required>
        <option value="adhered">Adhered</option>
        <option value="missed">Missed</option>
    </select>
    <input type="submit" value="Track Adherence">
</form>

    
    
</body>
</html>
