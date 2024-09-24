<?php
$servername = "localhost"; // Change this if your database is hosted elsewhere
$username = "root"; // Change this if necessary
$password = ""; // Change this if necessary
$dbname = "student_tracking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term
$searchTerm = isset($_POST['query']) ? $_POST['query'] : '';

// Prepare the SQL query
$sql = "SELECT student_id, CONCAT(last_name, ', ', first_name, ' ', middle_name) AS full_name, section, year_level, program FROM student WHERE student_id LIKE ? OR last_name LIKE ? OR first_name LIKE ? OR middle_name LIKE ?";
$stmt = $conn->prepare($sql);
$likeTerm = "%$searchTerm%";
$stmt->bind_param("ssss", $likeTerm, $likeTerm, $likeTerm, $likeTerm);
$stmt->execute();
$result = $stmt->get_result();

// Fetch data and return as table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['student_id']}</td>
            <td>{$row['full_name']}</td>
            <td>{$row['section']}</td>
            <td>{$row['year_level']}</td>
            <td>{$row['program']}</td>
          </tr>";
}

$stmt->close();
$conn->close();
?>
