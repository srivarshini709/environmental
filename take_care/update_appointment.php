<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    // Validate input
    if (empty($appointment_id) || empty($status)) {
        die('Invalid input.');
    }

    // Prepare the SQL query to update the appointment status
    $sql = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    // Bind parameters and execute the statement
    $stmt->bind_param('si', $status, $appointment_id);
    if ($stmt->execute() === false) {
        die('Execute failed: ' . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect to the view appointments page with a success message
    header("Location: view_appointments.php?status=success");
    exit();
} else {
    // If form is not submitted properly, redirect to the view appointments page
    header("Location: view_appointments.php?status=error");
    exit();
}
?>
