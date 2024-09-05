<?php
// Include database connection
include 'db_connection.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Initialize filter variables
$date_filter = isset($_POST['date_filter']) ? $_POST['date_filter'] : '';
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

// Prepare the SQL query with filters
$sql = "SELECT a.id, a.appointment_date, a.status, CONCAT(d.first_name, ' ', d.last_name) AS doctor_name, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM appointments a
        JOIN users d ON a.doctor_id = d.id
        JOIN users p ON a.patient_id = p.id
        WHERE 1=1";

if ($date_filter) {
    $sql .= " AND a.appointment_date = ?";
}
if ($status_filter) {
    $sql .= " AND a.status = ?";
}

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

// Bind parameters based on filters
$param_types = '';
$params = [];
if ($date_filter) {
    $param_types .= 's';
    $params[] = $date_filter;
}
if ($status_filter) {
    $param_types .= 's';
    $params[] = $status_filter;
}

if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Execute failed: ' . $conn->error);
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments - TakeCare Hospital Management System</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file for styling -->
    <style>
        /* Your existing CSS styling */
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
            <a href="contact_us.php">Contact Us</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="main-content">
            <h1>View Appointments</h1>

            <!-- Filter Form -->
            <div class="card">
                <h3>Filter Appointments</h3>
                <form action="view_appointments.php" method="POST">
                    <div class="form-group">
                        <label for="date_filter">Date</label>
                        <input type="date" id="date_filter" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>">
                    </div>
                    <div class="form-group">
                        <label for="status_filter">Status</label>
                        <select id="status_filter" name="status_filter">
                            <option value="">All</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="canceled" <?php echo $status_filter === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Filter">
                    </div>
                </form>
            </div>

            <!-- Appointments Table -->
            <div class="card">
                <h3>Appointments</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($appointment = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                                <td class="actions">
                                    <form action="update_appointment.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['id']); ?>">
                                        <select name="status" required>
                                            <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo $appointment['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="canceled" <?php echo $appointment['status'] === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                        </select>
                                        <input type="submit" name="update_status" value="Update Status">
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
