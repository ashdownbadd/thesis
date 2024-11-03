<?php
// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "student_tracking";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed.']));
}

// Fetch alerts from the log file
$file = 'emergency_alerts.log';
if (!file_exists($file)) {
    echo json_encode([]);
    exit();
}

$alerts = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$alertData = [];

foreach ($alerts as $line) {
    // Updated log format: "[timestamp] Emergency alert (emergency_type) from first_name middle_name last_name extension (Student No: student_no) - Year Level: year_level, Section: section, Program: program. Mobile: mobile_no, Email: email. Emergency Contact: emergency_name (emergency_no)"
    if (preg_match('/\[(.*?)\] Emergency alert \((.*?)\) from (.*?) (.*?) (.*?) (.*?) \(Student No: (.*?)\) - Year Level: (.*?), Section: (.*?), Program: (.*?). Mobile: (.*?), Email: (.*?). Emergency Contact: (.*?) \((.*?)\)/', $line, $matches)) {
        $alertData[] = [
            'timestamp' => $matches[1],
            'emergency_type' => $matches[2],
            'first_name' => $matches[3],
            'middle_name' => $matches[4],
            'last_name' => $matches[5],
            'extension' => $matches[6],
            'student_no' => $matches[7],
            'year_level' => $matches[8],
            'section' => $matches[9],
            'program' => $matches[10],
            'mobile_no' => $matches[11],
            'email' => $matches[12],
            'emergency_name' => $matches[13],
            'emergency_no' => $matches[14]
        ];
    }
}

// Sort alerts by timestamp ascending
usort($alertData, function($a, $b) {
    return strtotime($a['timestamp']) - strtotime($b['timestamp']);
});

echo json_encode($alertData);

$conn->close();
?>
