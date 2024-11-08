<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.html");
    exit();
}

$username = $_SESSION['username'];
$last_name = $_SESSION['last_name'];
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$extension = $_SESSION['extension'];
$mobile_no = $_SESSION['mobile_no'];
$email = $_SESSION['email'];
$year_level = $_SESSION['year_level'];
$section = $_SESSION['section'];
$program = $_SESSION['program'];
$student_id = $_SESSION['student_id'];

date_default_timezone_set('Asia/Manila');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="icon_planet.jpg">
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card__border">
            <img src="icon.jpg" alt="card image" class="card__img">
        </div>

        <h3 class="card__name">
            <?php 
                echo htmlspecialchars($_SESSION['first_name']) . ' ' . 
                     htmlspecialchars($_SESSION['middle_name']) . ' ' . 
                     htmlspecialchars($last_name); 
            ?>
        </h3>
        <span class="card__profession"><?php echo htmlspecialchars($program); ?></span>

        <div class="card__social" id="card-social">
            <div class="card__social-control">
                <div class="card__social-toggle" id="card-toggle">
                    <i class="ri-add-line"></i>
                </div>
                <span class="card__social-text">See More</span>
                <ul class="card__social-list">
                    <a href="updateform.html?last_name=<?php echo urlencode($last_name); ?>&first_name=<?php echo urlencode($first_name); ?>&middle_name=<?php echo urlencode($middle_name); ?>&extension=<?php echo urlencode($extension); ?>&mobile_no=<?php echo urlencode($mobile_no); ?>&email=<?php echo urlencode($email); ?>&year_level=<?php echo urlencode($year_level); ?>&section=<?php echo urlencode($section); ?>&program=<?php echo urlencode($program); ?>&student_id=<?php echo urlencode($student_id); ?>" class="card__social-link pen-icon">
                        <div class="circle">
                            <i class="fa fa-pen"></i>
                        </div>
                    </a>

                    <a href="manualguide.html" class="card__social-link" id="settings">
                    <i class="fas fa-book"></i>
                    </a>
                    <a href="logout.php" class="card__social-link">
                        <i class="fas fa-power-off"></i>
                    </a>
                </ul>
            </div>
        </div>
    </div>

<!-- Emergency Buttons Section -->
<div class="emergency-buttons">
    <h4>TYPE OF EMERGENCY</h4>

    <div class="button-container">
        <div class="button-row">
            <button class="emergency-button" id="medicalEmergency" title="Medical Emergency" data-emergency-type="Medical Emergency">
                <i class="fas fa-heartbeat"></i>
            </button>

            <button class="emergency-button" id="violenceEmergency" title="Violence Emergency" data-emergency-type="Violence Emergency">
                <i class="fas fa-hand-rock"></i>
            </button>

            <button class="emergency-button" id="fireEmergency" title="Fire Emergency"  data-emergency-type="Fire Emergency">
                <i class="fas fa-fire"></i>
            </button>
        </div>

        <div class="button-row">
            <button class="emergency-button" id="security" title="Security Threat" data-emergency-type="Security Threat">
                <i class="fas fa-shield-alt"></i>
            </button>

            <button class="emergency-button" id="powerOutage" title="Power Outage" data-emergency-type="Power Outage">
                <i class="fas fa-bolt"></i>
            </button>

            <button class="emergency-button" id="stranded" title="Stranded"  data-emergency-type="Stranded">
                <i class="fas fa-map-marker"></i>
            </button>
        </div>
    </div>
</div>
<!-- End of Emergency Buttons Section -->


    <!-- Important Notice Modal -->
<div id="noticeModal" class="modals">
    <div class="modal-contents">
        <h2>Important Notice</h2>
        <br>
        <p>Please, read this carefully:</p>
        <br>
        <p>Using the emergency buttons is a serious matter. Pranking or joking around with these buttons will have corresponding punishments. We rely on these alerts to ensure everyone's safety, and misuse can lead to unnecessary panic and resource allocation. Please use them responsibly.</p>
        <br>
        <button id="understoodButton" class="modal-button">Understood</button>
        <br>
        <label>
            <input type="checkbox" id="dontShowAgainCheckbox">
            Don't show this again.
        </label>
    </div>
    
</div>

</div>

<div id="logoutModal" class="modal">
    <div class="modal-content">
        <span class="close" id="modalClose">&times;</span>
        <h2>Sign Out</h2>
        <p>Are you sure you want to logout?</p>
        <button id="confirmLogout">Yes</button>
        <button id="cancelLogout">Cancel</button>
    </div>
</div>

<?php
$current_hour = date("H");

if ($current_hour < 12) {
    $greeting = "Good Morning";
} elseif ($current_hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

$last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : 'Student';

echo "<h1><span class='greeting'>$greeting, </span><span class='name'>" . strtoupper(htmlspecialchars($first_name)) . "</span></h1>";
?>


<script src="profile.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let locationAllowed = false;
        let watchId = null;

        function sendLocationToServer(lat, lon, allowed) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_location.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Location status updated:", xhr.responseText);
                }
            };
            xhr.send(`latitude=${lat}&longitude=${lon}&allowed=${allowed}`);
        }

        function requestLocationPermission() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        locationAllowed = true;
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        sendLocationToServer(latitude, longitude, true);
                        watchId = navigator.geolocation.watchPosition(updatePosition, handleError, {
                            enableHighAccuracy: true,
                            maximumAge: 30000,
                            timeout: 27000
                        });
                    },
                    (error) => {
                        if (error.code === error.PERMISSION_DENIED) {
                            sendLocationToServer(null, null, false);
                        } else {
                            console.error("Geolocation error:", error);
                        }
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function updatePosition(position) {
            if (locationAllowed) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                sendLocationToServer(latitude, longitude, true);
            }
        }

        function handleError(error) {
            console.error("Geolocation error:", error);
        }

        requestLocationPermission();

        window.addEventListener('beforeunload', () => {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
            }
        });
    });

    function sendEmergencyAlert(emergencyType) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "send_alert.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Sending just the emergency type
    const data = `emergency_type=${encodeURIComponent(emergencyType)}`;
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText); // Alert the response from the server
        }
    };
    xhr.send(data);
}

    document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.emergency-button');
    
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const emergencyType = button.getAttribute('title');
            const alertMessage = `${emergencyType}`;
            
            // Send emergency alert with user information
            sendEmergencyAlert(alertMessage, emergencyType);
        });
    });

    // Other existing code...
});


document.addEventListener('DOMContentLoaded', () => {
    const noticeModal = document.getElementById('noticeModal');
    const dontShowAgainCheckbox = document.getElementById('dontShowAgainCheckbox');

    // Check localStorage for the user's preference
    if (localStorage.getItem('dontShowNotice') !== 'true') {
        noticeModal.style.display = 'block'; // Show the modal
    }

    // Restore the checkbox state if it exists
    if (localStorage.getItem('dontShowAgainChecked') === 'true') {
        dontShowAgainCheckbox.checked = true;
    }

    // Close modal when "Understood" button is clicked
    document.getElementById('understoodButton').addEventListener('click', () => {
        // Store the checkbox state in localStorage
        localStorage.setItem('dontShowNotice', dontShowAgainCheckbox.checked);
        localStorage.setItem('dontShowAgainChecked', dontShowAgainCheckbox.checked);
        closeModal();
    });
});

function closeModal() {
    const noticeModal = document.getElementById('noticeModal');
    noticeModal.style.display = 'none';
}

const ws = new WebSocket('ws://localhost:8080');

    // Function to trigger emergency with user data
    function triggerEmergency(emergencyType) {
        // Assuming these details are accessible in profile.php as JavaScript variables
        const userData = {
        student_id: "<?php echo $student_id; ?>",
        last_name: "<?php echo $last_name; ?>",
        first_name: "<?php echo $first_name; ?>",
        middle_name: "<?php echo $middle_name; ?>",
        extension: "<?php echo $extension; ?>",
        mobile_no: "<?php echo $mobile_no; ?>",
        email: "<?php echo $email; ?>",
        year_level: "<?php echo $year_level; ?>",
        section: "<?php echo $section; ?>",
        program: "<?php echo $program; ?>"
    };

        if (ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({
                type: 'emergency',
                emergencyType: emergencyType,
                ...userData
            }));
        } else {
            console.error("WebSocket connection is not open.");
        }
    }

    // Add event listeners to emergency buttons
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.emergency-button').forEach(button => {
            button.addEventListener('click', () => {
                const emergencyType = button.getAttribute('data-emergency-type');
                triggerEmergency(emergencyType);
            });
        });
    });

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.emergency-button').forEach(button => {
        button.addEventListener('click', () => {
            const emergencyType = button.getAttribute('data-emergency-type');
            triggerEmergency(emergencyType);
        });
    });
});
</script>

</body>
</html>
