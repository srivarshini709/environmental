<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.html");
    exit();
}

// Get the drug ID from the query parameter
if (!isset($_GET['drug_id']) || !is_numeric($_GET['drug_id'])) {
    header("Location: pharmacy_dashboard.php");
    exit();
}
$drug_id = $_GET['drug_id'];

// Fetch the drug details
$sql = "SELECT * FROM drugs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $drug_id);
$stmt->execute();
$result = $stmt->get_result();
$drug = $result->fetch_assoc();

if (!$drug) {
    header("Location: pharmacy_dashboard.php");
    exit();
}

// Handle drug update
if (isset($_POST['update_drug'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE drugs SET name = ?, description = ?, quantity = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $name, $description, $quantity, $price, $drug_id);

    if ($stmt->execute()) {
        header("Location: pharmacy_dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Edit Drug - TakeCare Hospital Management System</title>
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
            <h1>Edit Drug</h1>

            <!-- Edit Drug Form -->
            <div class="card">
                <h3>Update Drug Details</h3>
                <form action="edit_drug.php?drug_id=<?php echo htmlspecialchars($drug['id']); ?>" method="POST">
                    <div class="form-group">
                        <label for="name">Drug Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($drug['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($drug['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($drug['quantity']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($drug['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="update_drug" value="Update Drug">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
