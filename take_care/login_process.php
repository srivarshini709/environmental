<?php
// Database connection settings
$servername = "localhost"; // Change if needed
$username = "root";        // Change if needed
$password = "";            // Change if needed
$dbname = "takecare";      // Change if needed

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Collect and sanitize input data
$user_name = trim($_POST['user_name']);
$password = trim($_POST['password']);

// Validate the input
if (empty($user_name) || empty($password)) {
    echo "Both fields are required!";
    exit();
}

// Prepare the SQL statement to prevent SQL injection
$sql = "SELECT id, user_name, password, role FROM users WHERE user_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on user role
        switch ($user['role']) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'doctor':
                header("Location: doctor_dashboard.php");
                break;
            case 'staff':
                header("Location: staff_dashboard.php");
                break;
            case 'nurse':
                header("Location: nurse_dashboard.php");
                break;
            case 'patient':
                header("Location: patient_dashboard.php");
                break;
            default:
                echo "Unknown role!";
                break;
        }
        exit();
    } else {
        echo "Invalid password!";
    }
} else {
    echo "Username does not exist!";
}

// Close connections
$stmt->close();
$conn->close();
?>
