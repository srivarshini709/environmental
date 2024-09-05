<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Handle billing addition
if (isset($_POST['add_billing'])) {
    $patient_id = $_POST['patient_id'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $sql = "INSERT INTO billing (patient_id, amount, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $patient_id, $amount, $description);

    if ($stmt->execute()) {
        echo "Billing added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle billing update
if (isset($_POST['update_billing'])) {
    $billing_id = $_POST['billing_id'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $sql = "UPDATE billing SET amount = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dsi", $amount, $description, $billing_id);

    if ($stmt->execute()) {
        echo "Billing updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle billing deletion
if (isset($_GET['delete_billing'])) {
    $billing_id = $_GET['delete_billing'];

    $sql = "DELETE FROM billing WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $billing_id);

    if ($stmt->execute()) {
        echo "Billing deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch billing records
$sql = "SELECT b.id, b.patient_id, b.amount, b.description, u.first_name AS patient_name
        FROM billing b
        JOIN users u ON b.patient_id = u.id";
$result = $conn->query($sql);

// Fetch patients for dropdown
$patients_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'patient'";
$patients_result = $conn->query($patients_query);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Sidebar content -->
        </div>
        <div class="main-content">
            <h1>Billing</h1>

            <!-- Add Billing Form -->
            <div class="card">
                <h3>Add New Billing</h3>
                <form action="billing.php" method="POST">
                    <div class="form-group">
                        <label for="patient_id">Patient</label>
                        <select id="patient_id" name="patient_id" required>
                            <?php while ($patient = $patients_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($patient['id']); ?>"><?php echo htmlspecialchars($patient['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" name="amount" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="add_billing" value="Add Billing">
                    </div>
                </form>
            </div>

            <!-- Billing Records Table -->
            <div class="card">
                <h3>Billing Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($billing = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($billing['id']); ?></td>
                            <td><?php echo htmlspecialchars($billing['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($billing['amount']); ?></td>
                            <td><?php echo htmlspecialchars($billing['description']); ?></td>
                            <td class="actions">
                                <a href="edit_billing.php?billing_id=<?php echo htmlspecialchars($billing['id']); ?>">Edit</a> | 
                                <a href="billing.php?delete_billing=<?php echo htmlspecialchars($billing['id']); ?>" onclick="return confirm('Are you sure you want to delete this billing record?');">Delete</a>
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
