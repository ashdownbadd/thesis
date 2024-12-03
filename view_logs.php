<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "student_tracking";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$programs = $conn->query("SELECT DISTINCT program FROM logs");
$statuses = $conn->query("SELECT DISTINCT status FROM logs");
$emergency_types = $conn->query("SELECT DISTINCT emergency_type FROM logs");

$program_filter = isset($_GET['program']) ? $_GET['program'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$emergency_type_filter = isset($_GET['emergency_type']) ? $_GET['emergency_type'] : '';

$filter_conditions = [];
if ($program_filter) $filter_conditions[] = "program = '$program_filter'";
if ($status_filter) $filter_conditions[] = "status = '$status_filter'";
if ($emergency_type_filter) $filter_conditions[] = "emergency_type = '$emergency_type_filter'";

$filter_sql = '';
if (!empty($filter_conditions)) {
    $filter_sql = "WHERE " . implode(" AND ", $filter_conditions);
}

$sql = "SELECT * FROM logs $filter_sql ORDER BY date DESC, time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="icon_planet.jpg">

    <title>View Logs</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #CEE470;
            background-image: url('background.svg');
            background-size: cover;
            color: #333;
        }

        .filter-container {
            display: flexbox;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px auto;
            padding: 20px;
            border-radius: 12px;
            width: calc(100% - 40px);
            max-width: 900px;
            box-sizing: border-box;
        }

        .filter-container input,
        .filter-container select {
            padding: 10px;
            font-size: 14px;
            border-radius: 8px;
            width: 180px;
            height: 35px;
            border: none;
            box-sizing: border-box;
            margin: 5px 0;
        }

        .filter-container button {
            padding: 10px 15px;
            background-color: #6D4AD0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .filter-container button:hover {
            background-color: #6f4db5;
        }

        .log-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
        }

        .log-header,
        .log-row {
            display: flex;
            align-items: center;
            padding: 15px 20px;
        }

        .log-header {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            font-size: 14px;
            display: flex;
            justify-content: center;
            text-align: center;
        }

        .log-header div {
            flex: 1;
            text-align: center;
        }

        .log-row {
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease-in-out;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .log-row div {
            flex: 1;
            font-size: 14px;
            color: #555;
            text-align: center;
        }

        @media (max-width: 480px) {
            .filter-container {
                flex-direction: column;
                gap: 10px;
            }

            .filter-container input,
            .filter-container select {
                width: 100%;
            }

            .log-header,
            .log-row {
                flex-direction: column;
                text-align: center;
                padding: 10px;
            }

            .log-header div,
            .log-row div {
                width: 100%;
            }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .filter-container {
                gap: 10px;
                padding: 15px;
            }

            .filter-container input,
            .filter-container select {
                width: 150px;
            }

            .filter-container button {
                padding: 8px 12px;
            }

            .log-header,
            .log-row {
                padding: 10px 15px;
            }

            .log-header div,
            .log-row div {
                font-size: 12px;
            }
        }

        #notification-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            pointer-events: none;
        }

        .notification-box {
            background-color: #E53333;
            color: black;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .notification-image {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }

        .notification-box h4 {
            margin: 0 0 5px;
            font-size: 60px;
            color: white;
            width: 100%;
            text-transform: uppercase;
            font-family: 'Impact', sans-serif;
            text-rendering: optimizeLegibility;
        }

        .notification-box p {
            margin: 0 0 6px;
            font-size: 16px;
            color: white;
            width: 100%;
            font-family: Arial, Helvetica, sans-serif;
        }

        .track-button {
            background-color: #FFD42A;
            color: black;
            display: inline-flex;
            align-items: center;
            border: 2px solid black;
            justify-content: center;
            cursor: pointer;
            border-radius: 10px 10px 25px 25px;
            width: 120px;
            height: 40px;
            font-size: 24px;
            transition: background-color 0.3s;
            z-index: 9999;
            display: none;
            position: fixed;
            bottom: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .track-button:hover {
            background-color: #0056b3;
        }

        .track-button i {
            margin-right: 5px;
        }

        .dismiss-button {
            background-color: transparent;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 25px;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 9999;
            display: none;
        }

        .dismiss-button i {
            pointer-events: none;
        }
    </style>

    <script>
        function autoSubmit() {
            document.getElementById('filterForm').submit();
        }

        function clearFilters() {
            document.getElementById('program').value = '';
            document.getElementById('status').value = '';
            document.getElementById('emergency_type').value = '';
            document.getElementById('filterForm').submit();
        }
    </script>
</head>

<body>

    <div id="notification-container"></div>

    <button id="trackLocation" class="track-button" style="display: none;">
        <i class="fa-solid fa-location-crosshairs"></i>
    </button>

    <button class="dismiss-button" id="dismissNotification">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <form id="filterForm" method="GET" class="filter-container">
        <select name="program" id="program" onchange="autoSubmit()">
            <option value="">Programs</option>
            <?php while ($row = $programs->fetch_assoc()): ?>
                <option value="<?php echo $row['program']; ?>" <?php echo ($program_filter == $row['program']) ? 'selected' : ''; ?>>
                    <?php echo $row['program']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="status" id="status" onchange="autoSubmit()">
            <option value="">Status</option>
            <?php while ($row = $statuses->fetch_assoc()): ?>
                <option value="<?php echo $row['status']; ?>" <?php echo ($status_filter == $row['status']) ? 'selected' : ''; ?>>
                    <?php echo $row['status']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="emergency_type" id="emergency_type" onchange="autoSubmit()">
            <option value="">Emergency Types</option>
            <?php while ($row = $emergency_types->fetch_assoc()): ?>
                <option value="<?php echo $row['emergency_type']; ?>" <?php echo ($emergency_type_filter == $row['emergency_type']) ? 'selected' : ''; ?>>
                    <?php echo $row['emergency_type']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="button" onclick="clearFilters()">Clear Filters</button>
    </form>

    <div class="log-container">
        <div class="log-header">
            <div>No.</div>
            <div>Date</div>
            <div>Time</div>
            <div>Authorized Personnel</div>
            <div>Student</div>
            <div>Program</div>
            <div>Emergency Type</div>
            <div>Status</div>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="log-row">
                    <div><?php echo $row['id']; ?></div>
                    <div><?php echo $row['date']; ?></div>
                    <div><?php echo $row['time']; ?></div>
                    <div><?php echo $row['actor']; ?></div>
                    <div><?php echo $row['student_name']; ?></div>
                    <div><?php echo $row['program']; ?></div>
                    <div><?php echo $row['emergency_type']; ?></div>
                    <div><?php echo $row['status']; ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="log-row">
                <div colspan="8">No logs found</div>
            </div>
        <?php endif; ?>
    </div>

    <?php $conn->close(); ?>

    <script>
        const ws = new WebSocket('ws://192.168.0.101:8080');

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === 'emergency') {
                setTimeout(() => {
                    if (navigator.vibrate) {
                        navigator.vibrate(500);
                    }
                }, 100);
                createNotificationBox(data);
            }
        };

        ws.onerror = (error) => {
            console.error("WebSocket error:", error);
        };

        function createNotificationBox(data) {
            const container = document.getElementById('notification-container');

            container.innerHTML = '';

            const notificationBox = document.createElement('div');
            notificationBox.classList.add('notification-box');
            notificationBox.id = 'activeNotification';

            notificationBox.innerHTML = `
<img src="alert.png" alt="Notification Image" class="notification-image">
<h4>${data.emergencyType}</h4>
<p><strong>Name:</strong> ${data.first_name} ${data.middle_name} ${data.last_name} ${data.extension}</p>
<p><strong>Year & Section:</strong> ${data.year_level} - ${data.section}</p>
<p><strong>Emergency Contact:</strong> ${data.emergency_no}</p>
`;

            container.appendChild(notificationBox);

            const emergencySound = new Audio('emergency_sound.mp3');
            emergencySound.loop = true;
            emergencySound.play().catch((error) => {
                console.error("Failed to play emergency sound:", error);
            });
            const trackButton = document.getElementById('trackLocation');
            const dismissButton = document.getElementById('dismissNotification');

            trackButton.style.display = 'block';
            dismissButton.style.display = 'block';

            trackButton.addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {

                            const fullName = `${data.first_name} ${data.middle_name} ${data.last_name} ${data.extension}`.trim();
                            const year = data.year_level;
                            const section = data.section;
                            const program = data.program;
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;
                            const alertLat = data.latitude;
                            const alertLng = data.longitude;
                            const emergencyName = data.emergency_name;
                            const emergencyNo = data.emergency_no;
                            const mobileNo = data.mobile_no;
                            const emergencyType = data.emergencyType;
                            const userName = document.getElementById('userName').textContent;

                            const url = `mapPage.html?alertLat=${encodeURIComponent(alertLat)}&alertLng=${encodeURIComponent(alertLng)}&userLat=${encodeURIComponent(userLat)}&userLng=${encodeURIComponent(userLng)}&emergencyName=${encodeURIComponent(emergencyName)}&emergencyNo=${encodeURIComponent(emergencyNo)}&mobileNo=${encodeURIComponent(mobileNo)}&fullName=${encodeURIComponent(fullName)}&userName=${encodeURIComponent(userName)}&year=${encodeURIComponent(year)}&section=${encodeURIComponent(section)}&program=${encodeURIComponent(program)}&emergencyType=${encodeURIComponent(emergencyType)}`;

                            location.href = url;
                        },
                        (error) => {
                            console.error("Error getting user location:", error);
                            alert("Failed to get location. Please enable location services.");
                        }
                    );
                } else {
                    alert("Geolocation is not supported by your browser.");
                }

                notificationBox.remove();
                trackButton.style.display = 'none';
                dismissButton.style.display = 'none';
                emergencySound.pause();
                emergencySound.currentTime = 0;
            });

            dismissButton.addEventListener('click', function() {
                notificationBox.remove();
                trackButton.style.display = 'none';
                dismissButton.style.display = 'none';
                emergencySound.pause();
                emergencySound.currentTime = 0;
            });
        }
    </script>

</body>

</html>