<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Handle adding a new medication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medication'])) {
    $medication_name = $_POST['medication_name'];
    $dosage = $_POST['dosage'];
    $stock_quantity = $_POST['stock_quantity'];
    $price = $_POST['price'];

    if (!empty($medication_name) && !empty($dosage) && !empty($stock_quantity) && !empty($price)) {
        $sql = "INSERT INTO medications (medication_name, dosage, stock_quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $medication_name, $dosage, $stock_quantity, $price);

        if ($stmt->execute()) {
            $success_message = "Medication added successfully.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Handle deleting a medication
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM medications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $success_message = "Medication deleted successfully.";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}

// Fetch medication records
$sql = "SELECT * FROM medications ORDER BY medication_name ASC";
$result = $conn->query($sql);
$medications = $result->fetch_all(MYSQLI_ASSOC);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy - TakeCare Hospital Management System</title>
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
        .table-container {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
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
        .action-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
        }
        .action-btn:hover {
            background-color: #c82333;
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
            <h1>Pharmacy Management</h1>
            <div class="card">
                <h3>Add New Medication</h3>
                <form action="pharmacy.php" method="POST">
                    <div class="form-group">
                        <label for="medication_name">Medication Name</label>
                        <input type="text" id="medication_name" name="medication_name" required>
                    </div>
                    <div class="form-group">
                        <label for="dosage">Dosage</label>
                        <input type="text" id="dosage" name="dosage" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_medication" value="Add Medication">
                    </div>
                </form>
                <?php if (isset($success_message)): ?>
                    <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <h3>Medication Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Dosage</th>
                            <th>Stock Quantity</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medications as $medication): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($medication['id']); ?></td>
                            <td><?php echo htmlspecialchars($medication['medication_name']); ?></td>
                            <td><?php echo htmlspecialchars($medication['dosage']); ?></td>
                            <td><?php echo htmlspecialchars($medication['stock_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($medication['price']); ?></td>
                            <td>
                                <a href="edit_medication.php?id=<?php echo htmlspecialchars($medication['id']); ?>" class="action-btn">Edit</a>
                                <a href="pharmacy.php?delete_id=<?php echo htmlspecialchars($medication['id']); ?>" class="action-btn">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
