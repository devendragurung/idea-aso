<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Search and CSV Export</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .container {
            max-width: 800px;
        }
        .alert {
            display: none;
        }
        table {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Search for Teacher by ID or Name</h1>

    <!-- Search Form -->
    <div class="card p-4 shadow-sm mx-auto" style="max-width: 600px;">
        <form id="teacherForm" class="mb-4">
            <div class="mb-3">
                <label for="name" class="form-label">Name (Optional):</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter teacher name (optional)">
            </div>

            <div class="mb-3">
                <label for="teacherIDStart" class="form-label">Teacher ID (Start):</label>
                <input type="number" class="form-control" id="teacherIDStart" name="teacherIDStart" placeholder="e.g., 8" required>
            </div>

            <div class="mb-3">
                <label for="teacherIDEnd" class="form-label">Teacher ID (End):</label>
                <input type="number" class="form-control" id="teacherIDEnd" name="teacherIDEnd" placeholder="e.g., 20" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Search</button>
        </form>
    </div>
</div>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card text-center" style="width: 18rem;">
      <div class="card-body">
        <h5 class="card-title">Card with Links</h5>
        <p class="card-text">This is a card with three different links.</p>
        <!-- Links inside the card -->
        <a href="#" class="link-primary d-block mb-2">Primary Link</a>
        <a href="#" class="link-secondary d-block mb-2">Secondary Link</a>
        <a href="#" class="link-success d-block">Success Link</a>
      </div>
    </div>
  </div>

<!-- Result Display Section -->
<div id="teacherResults" class="mt-4">
    <div class="alert alert-danger" role="alert" id="errorMessage">
        No results found for the given Teacher ID range or name.
    </div>

    <div class="alert alert-success" role="alert" id="successMessage">
        Results found! You can now download the CSV:
    </div>

    <!-- Table to Display Results -->
    <div id="teacherTableContainer" class="mb-4" style="display: none;">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>TeacherID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Payment Rate per Hour</th>
                </tr>
            </thead>
            <tbody id="teacherTableBody"></tbody>
        </table>
    </div>

    

    <!-- CSV Download Button -->
    <form method="POST" action="download_csv.php" id="downloadForm">
        <input type="hidden" name="teacherIDStart" id="teacherIDStartHidden">
        <input type="hidden" name="teacherIDEnd" id="teacherIDEndHidden">
        <input type="hidden" name="name" id="nameHidden">
        <button type="submit" class="btn btn-success" id="downloadCSV" style="display: none;">Download CSV</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript to handle the form submission and AJAX request
    document.getElementById('teacherForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Get the form values
        let teacherIDStart = document.getElementById('teacherIDStart').value;
        let teacherIDEnd = document.getElementById('teacherIDEnd').value;
        let name = document.getElementById('name').value;

        // Clear messages and hide download button
        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('successMessage').style.display = 'none';
        document.getElementById('downloadCSV').style.display = 'none';
        document.getElementById('teacherTableContainer').style.display = 'none';
        document.getElementById('teacherTableBody').innerHTML = ''; // Clear table

        // Perform an AJAX request to search_teacher.php
        fetch('search_teacher.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `teacherIDStart=${teacherIDStart}&teacherIDEnd=${teacherIDEnd}&name=${name}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.results.length > 0) {
                // Populate table with results
                let tableBody = document.getElementById('teacherTableBody');
                data.results.forEach(teacher => {
                    let row = `<tr>
                                <td>${teacher.teacher_id}</td>
                                <td>${teacher.name}</td>
                                <td>${teacher.email}</td>
                                <td>${teacher.payment_rate_per_hour}</td>
                               </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                // Show table and success message
                document.getElementById('teacherTableContainer').style.display = 'block';
                document.getElementById('successMessage').style.display = 'block';
                
                // Set hidden inputs for CSV download
                document.getElementById('teacherIDStartHidden').value = teacherIDStart;
                document.getElementById('teacherIDEndHidden').value = teacherIDEnd;
                document.getElementById('nameHidden').value = name;

                // Show download button
                document.getElementById('downloadCSV').style.display = 'block';
            } else {
                // Show error message if no results
                document.getElementById('errorMessage').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('errorMessage').textContent = 'An error occurred. Please try again.';
            document.getElementById('errorMessage').style.display = 'block';
        });
    });
</script>
</body>
</html>
