<?php
// Include database connection
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Example drug interactions (this should ideally be replaced with real-time AI-driven data)
$drug_1 = $_POST['drug_1'];
$drug_2 = $_POST['drug_2'];

$sql = "SELECT interaction_description FROM drug_interactions 
        WHERE (drug_1 = ? AND drug_2 = ?) OR (drug_1 = ? AND drug_2 = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $drug_1, $drug_2, $drug_2, $drug_1);
$stmt->execute();
$result = $stmt->get_result();

$interactions = [];
while ($row = $result->fetch_assoc()) {
    $interactions[] = $row['interaction_description'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interactions</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Drug Interaction Alerts</h1>
    <?php if (empty($interactions)): ?>
        <p>No interactions found.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($interactions as $interaction): ?>
                <li><?php echo htmlspecialchars($interaction); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
