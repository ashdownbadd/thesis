document.getElementById('searchButton').addEventListener('click', function() {
    const searchTerm = document.getElementById('searchInput').value;

    fetch(`fetch_student.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('studentTableBody');
            tableBody.innerHTML = ''; // Clear previous results

            data.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.student_id}</td>
                    <td>${student.full_name}</td>
                    <td>${student.section}</td>
                    <td>${student.year_level}</td>
                    <td>${student.program}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching data:', error));
});
