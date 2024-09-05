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

// Collect and sanitize input data
$role = $_POST['role'];
$user_name = trim($_POST['user_name']);
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Validate the input
if (empty($role) || empty($user_name) || empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    echo "All fields are required!";
    exit();
}

// Check if the username or email already exists
$sql = "SELECT * FROM users WHERE user_name = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_name, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Username or email already exists!";
    $stmt->close();
    $conn->close();
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert data into the database
$sql = "INSERT INTO users (role, user_name, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $role, $user_name, $first_name, $last_name, $email, $hashed_password);

if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
