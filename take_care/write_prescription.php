<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.html");
    exit();
}

// Fetch patients for dropdown in the prescription form
$patients_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'patient'";
$patients_result = $conn->query($patients_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $prescription_date = $_POST['prescription_date'];
    $medication = $_POST['medication'];
    $dosage = $_POST['dosage'];
    $instructions = $_POST['instructions'];

    // Validate inputs
    if (!$patient_id || !$prescription_date || !$medication || !$dosage) {
        $error_message = "All fields are required.";
    } else {
        // Insert prescription into database
        $sql = "INSERT INTO prescriptions (doctor_id, patient_id, prescription_date, medication, dosage, instructions) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $_SESSION['user_id'], $patient_id, $prescription_date, $medication, $dosage, $instructions);

        if ($stmt->execute()) {
            $success_message = "Prescription added successfully!";
        } else {
            $error_message = "Error adding prescription: " . $stmt->error;
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Prescription - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 800px;
        }
        .card h3 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: #d9534f;
        }
        .success {
            color: #5bc0de;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h3>Write Prescription</h3>
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php elseif (isset($success_message)): ?>
                <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <form action="write_prescription.php" method="POST">
                <div class="form-group">
                    <label for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id" required>
                        <option value="">Select Patient</option>
                        <?php while ($patient = $patients_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($patient['id']); ?>">
                                <?php echo htmlspecialchars($patient['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="prescription_date">Date</label>
                    <input type="date" id="prescription_date" name="prescription_date" required>
                </div>
                <div class="form-group">
                    <label for="medication">Medication</label>
                    <input type="text" id="medication" name="medication" required>
                </div>
                <div class="form-group">
                    <label for="dosage">Dosage</label>
                    <input type="text" id="dosage" name="dosage" required>
                </div>
                <div class="form-group">
                    <label for="instructions">Instructions</label>
                    <textarea id="instructions" name="instructions" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" value="Submit Prescription">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
