// Function to handle key press in input fields
document.getElementById('username').addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        authenticate(); // Call the authenticate function
    }
});

document.getElementById('password').addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        authenticate(); // Call the authenticate function
    }
});

// Existing authenticate function
function authenticate() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Check if username and password are "admin"
    if (username === "admin" && password === "admin") {
        // Redirect to index.html
        window.location.href = "index.html";
    } else {
        // Display error message
        alert("Invalid username or password. Please try again.");
    }
}
