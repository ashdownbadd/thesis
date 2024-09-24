// Initialize the map
const map = L.map('map').setView([13.76657, 122.23782], 13);

// Define map layers
const datavizDarkLayer = L.tileLayer('https://api.maptiler.com/maps/dataviz-dark/{z}/{x}/{y}.png?key=U3dUFcIn1f5s5ThM8lWG', {
  maxZoom: 18,
  attribution: '© MapTiler © OpenStreetMap contributors'
});

const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19
});

// Add default layer to the map
datavizDarkLayer.addTo(map);

// Add layer control
L.control.layers({
  "DataViz Dark": datavizDarkLayer,
  "OpenStreetMap": osmLayer
}).addTo(map);

// Fetch and display student data
fetch('fetch_students.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
        const searchInput = document.querySelector('.input');
        searchInput.addEventListener('input', () => filterStudents(data, searchInput.value));
        displayStudents(data);
        updateStudentTable(data);
    })
    .catch(error => {
        console.error('Error fetching students:', error);
    });

// Display students in the list
function displayStudents(students) {
  const resultsContainer = document.querySelector('#results');
  resultsContainer.innerHTML = '';

  students.forEach(student => {
    const studentCard = document.createElement('div');
    studentCard.className = 'student-card';
    studentCard.textContent = `${student.first_name} ${student.middle_name} ${student.last_name}`;
    studentCard.addEventListener('click', () => showStudentProfile(student));
    resultsContainer.appendChild(studentCard);
  });
}

// Filter students based on search input
function filterStudents(students, searchQuery) {
  const filteredStudents = students.filter(student => 
    `${student.first_name} ${student.middle_name} ${student.last_name}`.toLowerCase().includes(searchQuery.toLowerCase())
  );
  displayStudents(filteredStudents);
}

// Show detailed student profile
function showStudentProfile(student) {
  const profileContainer = document.createElement('div');
  profileContainer.className = 'profile-container';

  const profileContent = `
    <div class="profile-card">
      <h2>${student.first_name} ${student.middle_name} ${student.last_name}</h2>
      <p><strong>ID:</strong> ${student.student_id}</p>
      <p><strong>Email:</strong> ${student.email}</p>
      <p><strong>Phone:</strong> ${student.phone}</p>
      <button class="close-btn">Close</button>
    </div>
  `;
  profileContainer.innerHTML = profileContent;
  document.body.appendChild(profileContainer);

  const closeBtn = profileContainer.querySelector('.close-btn');
  closeBtn.addEventListener('click', () => document.body.removeChild(profileContainer));
}

// Request location permission and get the location
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else {
    alert("Geolocation is not supported by this browser.");
  }
}

// Handle successful location acquisition
function showPosition(position) {
  const latitude = position.coords.latitude;
  const longitude = position.coords.longitude;

  // Update map view
  map.setView([latitude, longitude], 15);

  // Place a marker on the map
  L.marker([latitude, longitude]).addTo(map)
    .bindPopup("<b>You are here</b>").openPopup();

  // Send location data to backend server
  fetch('update-location.php', {  // Adjust the PHP file path as needed
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      studentId: 'student123', // replace with actual student identifier
      latitude,
      longitude,
    }),
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
  })
  .catch(error => console.error('Error updating location:', error));
}

// Handle location errors
function showError(error) {
  switch (error.code) {
    case error.PERMISSION_DENIED:
      alert("User denied the request for Geolocation.");
      break;
    case error.POSITION_UNAVAILABLE:
      alert("Location information is unavailable.");
      break;
    case error.TIMEOUT:
      alert("The request to get user location timed out.");
      break;
    case error.UNKNOWN_ERROR:
      alert("An unknown error occurred.");
      break;
  }
}

// Fetch students and get the location when the page loads
window.onload = () => {
  fetchStudents();
  getLocation();
};

// Function to update the table with student data
function updateStudentTable(students) {
  const tableBody = document.querySelector('#student-table tbody');
  tableBody.innerHTML = '';

  students.forEach(student => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${student.student_id}</td>
      <td>${student.first_name} ${student.middle_name} ${student.last_name}</td>
      <td>${student.email}</td>
      <td>${student.phone}</td>
    `;
    tableBody.appendChild(row);
  });
}

// Fetch students function
function fetchStudents() {
  fetch('fetch_students.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Fetched data:', data); // Log data for debugging
      const searchInput = document.querySelector('.input');
      searchInput.addEventListener('input', () => filterStudents(data, searchInput.value));
      displayStudents(data);
      updateStudentTable(data); // Update the table with fetched data
    })
    .catch(error => console.error('Error fetching students:', error));
}