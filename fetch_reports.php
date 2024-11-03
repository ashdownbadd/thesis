<?php
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

// Fetch data from the student table
$sql = "SELECT student_id, 
CONCAT(last_name, ', ', first_name, ' ' , middle_name, ' ' , extension) AS full_name, mobile_no, email, year_level, section, program, emergency_type, emergency_name, emergency_no, report_date FROM emergency_reports";
$result = $conn->query($sql);

$reports = [];
if ($result->num_rows > 0) {
    // Fetch all rows and store them in the reports array
    while($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}

// Close the connection
$conn->close();

// Return the reports as JSON
header('Content-Type: application/json');
echo json_encode($reports);
?>
