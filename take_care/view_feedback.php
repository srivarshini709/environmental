<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Prepare and execute the query to fetch feedback
$sql = "SELECT f.id, u.user_name AS username, f.feedback_text, f.feedback_date
        FROM feedback f
        JOIN users u ON f.user_id = u.id";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->execute();
$feedback_result = $stmt->get_result();

if ($feedback_result === false) {
    die('Execute failed: ' . $conn->error);
}

// Check if there are any results
if ($feedback_result->num_rows === 0) {
    $feedback_result = null; // Set to null if no results
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
    <title>View Feedback</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>User Feedback</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Feedback</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($feedback_result): ?>
                <?php while ($row = $feedback_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                    <td><?php echo htmlspecialchars($row['feedback_date']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No feedback available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
