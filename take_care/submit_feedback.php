<?php
include 'db_connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];  // Get the logged-in user ID
    $feedback_text = $_POST['feedback_text'];

    // Prepare an SQL query to insert the feedback into the database
    $sql = "INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("is", $user_id, $feedback_text);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Thank you for your feedback!";
    } else {
        echo "Error submitting feedback. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
