<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_tracking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$student_id = $_SESSION['student_id'];
$current_date = date('Y-m-d');

$query = $conn->prepare("SELECT * FROM emergency_reports WHERE student_id = ? AND DATE(report_date) = ?");
$query->bind_param("ss", $student_id, $current_date);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "limited", "message" => "You have already sent an alert today."]);
} else {
    echo json_encode(["status" => "ok", "message" => "You can send an alert."]);
}

$query->close();
$conn->close();
