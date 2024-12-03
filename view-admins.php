<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_tracking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM admin WHERE id = '$deleteId'";

    if ($conn->query($deleteQuery) === TRUE) {
        echo "<script>
                alert('Admin deleted successfully!');
                window.location.href = 'view-admins.php';
              </script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorized Personnel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="icon_planet.jpg">
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

        .delete-btn {
            padding: 10px 15px;
            background-color: #6D4AD0;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #6f4db5;
        }

        @media (max-width: 480px) {

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

            .log-header,
            .log-row {
                padding: 10px 15px;
            }

            .log-header div,
            .log-row div {
                font-size: 12px;
            }
        }

        .circular-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            background-color: #CFFF5E;
            color: #161616;
            border: none;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
        }

        .circular-btn:hover {
            background-color: #6D4AD0;
            transform: scale(1.1);
        }

        .circular-btn a {
            color: white;
            text-decoration: none;
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
</head>

<body>

    <div id="notification-container"></div>

    <button id="trackLocation" class="track-button" style="display: none;">
        <i class="fa-solid fa-location-crosshairs"></i>
    </button>

    <button class="dismiss-button" id="dismissNotification">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="log-container">
        <div class="log-header">
            <div>ID</div>
            <div>Full Name</div>
            <div>Email</div>
            <div>Username</div>
            <div>Action</div>
        </div>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='log-row'>
                        <div>" . $row['id'] . "</div>
                        <div>" . htmlspecialchars($row['name']) . "</div>
                        <div>" . htmlspecialchars($row['email']) . "</div>
                        <div>" . htmlspecialchars($row['username']) . "</div>
                        <div>
                            <a href='view-admins.php?delete_id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this admin?');\">
                                <button class='delete-btn'>Delete</button>
                            </a>
                        </div>
                      </div>";
            }
        } else {
            echo "<div class='log-row'><div colspan='5'>No admins found.</div></div>";
        }
        ?>
    </div>

    <a href="reg-admin.html" class="circular-btn">
        <i class="fas fa-plus"></i>
    </a>

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

<?php
$conn->close();
?>