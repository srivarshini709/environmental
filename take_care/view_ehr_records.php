<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Check if patient_id is set
if (!isset($_GET['patient_id']) || empty($_GET['patient_id'])) {
    die('Patient ID is required.');
}

$patient_id = $_GET['patient_id'];

// Validate patient_id (ensure it's an integer)
if (!filter_var($patient_id, FILTER_VALIDATE_INT)) {
    die('Invalid Patient ID.');
}

// Prepare and execute the query to fetch EHR records
$sql = "SELECT * FROM ehr_records WHERE patient_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$ehr_records_result = $stmt->get_result();

if ($ehr_records_result === false) {
    die('Execute failed: ' . $conn->error);
}

// Check if there are any results
if ($ehr_records_result->num_rows === 0) {
    echo 'No records found for this patient.';
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View EHR Records - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
</head>
<body>
    <h1>EHR Records for Patient ID: <?php echo htmlspecialchars($patient_id); ?></h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Type</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($record = $ehr_records_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['id']); ?></td>
                <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                <td><?php echo htmlspecialchars($record['record_type']); ?></td>
                <td><?php echo htmlspecialchars($record['record_details']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
