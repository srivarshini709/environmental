<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.html");
    exit();
}

// Handle drug addition
if (isset($_POST['add_drug'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "INSERT INTO drugs (name, description, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $description, $quantity, $price);

    if ($stmt->execute()) {
        echo "Drug added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle drug updates
if (isset($_POST['update_drug'])) {
    $drug_id = $_POST['drug_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE drugs SET name = ?, description = ?, quantity = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $name, $description, $quantity, $price, $drug_id);

    if ($stmt->execute()) {
        echo "Drug updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle drug deletion
if (isset($_GET['delete_drug'])) {
    $drug_id = $_GET['delete_drug'];

    $sql = "DELETE FROM drugs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $drug_id);

    if ($stmt->execute()) {
        echo "Drug deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch drugs for listing
$sql = "SELECT * FROM drugs";
$drugs_result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard - TakeCare Hospital Management System</title>
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
        .form-group input, .form-group textarea {
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        .actions a {
            color: #007BFF;
            text-decoration: none;
            margin-right: 10px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Staff Dashboard</h2>
            <a href="staff_dashboard.php">Dashboard</a>
            <a href="patient_list.php">Patient List</a>
            <a href="appointment_schedule.php">Appointment Schedule</a>
            <a href="patient_records.php">Patient Records</a>
            <a href="pharmacy_dashboard.php">Pharmacy</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>Pharmacy Dashboard</h1>

            <!-- Add Drug Form -->
            <div class="card">
                <h3>Add New Drug</h3>
                <form action="pharmacy_dashboard.php" method="POST">
                    <div class="form-group">
                        <label for="name">Drug Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_drug" value="Add Drug">
                    </div>
                </form>
            </div>

            <!-- Drugs Table -->
            <div class="card">
                <h3>Drugs Inventory</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($drug = $drugs_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drug['id']); ?></td>
                            <td><?php echo htmlspecialchars($drug['name']); ?></td>
                            <td><?php echo htmlspecialchars($drug['description']); ?></td>
                            <td><?php echo htmlspecialchars($drug['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($drug['price']); ?></td>
                            <td class="actions">
                                <a href="edit_drug.php?drug_id=<?php echo htmlspecialchars($drug['id']); ?>">Edit</a> | 
                                <a href="pharmacy_dashboard.php?delete_drug=<?php echo htmlspecialchars($drug['id']); ?>" onclick="return confirm('Are you sure you want to delete this drug?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
