<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: signin.html");
    exit();
}

// Database connection
$servername = "localhost"; // Adjust if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "student_tracking"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the POST request
$emergency_type = isset($_POST['emergency_type']) ? $_POST['emergency_type'] : '';
$student_id = $_SESSION['student_id'];
$last_name = $_SESSION['last_name'];
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$extension = $_SESSION['extension'];
$mobile_no = $_SESSION['mobile_no'];
$email = $_SESSION['email'];
$year_level = $_SESSION['year_level'];
$section = $_SESSION['section'];
$program = $_SESSION['program'];
$emergency_name = 'Emergency Contact'; // This could be a specific field or static value

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO emergency_reports (student_id, last_name, first_name, middle_name, extension, mobile_no, email, year_level, section, program, emergency_type, emergency_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssssssssss", $student_id, $last_name, $first_name, $middle_name, $extension, $mobile_no, $email, $year_level, $section, $program, $emergency_type, $emergency_name);

// Execute the statement
if ($stmt->execute()) {
    echo "Emergency report submitted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
