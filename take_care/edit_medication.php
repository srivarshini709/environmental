<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Handle the update of a medication entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medication'])) {
    $medication_id = $_POST['medication_id'];
    $medication_name = $_POST['medication_name'];
    $dosage = $_POST['dosage'];
    $stock_quantity = $_POST['stock_quantity'];
    $price = $_POST['price'];

    if (!empty($medication_id) && !empty($medication_name) && !empty($dosage) && !empty($stock_quantity) && !empty($price)) {
        $sql = "UPDATE medications SET medication_name = ?, dosage = ?, stock_quantity = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidi", $medication_name, $dosage, $stock_quantity, $price, $medication_id);

        if ($stmt->execute()) {
            $success_message = "Medication updated successfully.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Fetch the medication entry to edit
if (isset($_GET['id'])) {
    $medication_id = $_GET['id'];

    $sql = "SELECT * FROM medications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $medication_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $medication = $result->fetch_assoc();
    } else {
        header("Location: pharmacy.php");
        exit();
    }
} else {
    header("Location: pharmacy.php");
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medication - TakeCare Hospital Management System</title>
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
        }
        .sidebar {
            width: 250px;
            background-color: #007BFF;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            margin-top: 0;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 15px 0;
        }
        .sidebar a:hover {
            background-color: #0056b3;
            padding: 10px;
            border-radius: 4px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .main-content h1 {
            margin-top: 0;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
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
        .form-group input, .form-group select {
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
        .message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="view_appointments.php">View Appointments</a>
            <a href="manage_records.php">Manage Records</a>
            <a href="generate_reports.php">Generate Reports</a>
            <a href="billing.php">Billing</a>
            <a href="pharmacy.php">Pharmacy</a>
            <a href="contact_us.php">Contact Us</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Edit Medication</h1>
            <div class="card">
                <h3>Update Medication Details</h3>
                <form action="edit_medication.php" method="POST">
                    <input type="hidden" name="medication_id" value="<?php echo htmlspecialchars($medication['id']); ?>">
                    <div class="form-group">
                        <label for="medication_name">Medication Name</label>
                        <input type="text" id="medication_name" name="medication_name" value="<?php echo htmlspecialchars($medication['medication_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dosage">Dosage</label>
                        <input type="text" id="dosage" name="dosage" value="<?php echo htmlspecialchars($medication['dosage']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($medication['stock_quantity']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($medication['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="update_medication" value="Update Medication">
                    </div>
                </form>
                <?php if (isset($success_message)): ?>
                    <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
