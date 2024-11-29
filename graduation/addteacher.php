<?php
include('database.php');

// Add teacher logic
if (isset($_POST['add_teacher'])) {
    $name = $_POST['teacher-name'];
    $email = $_POST['teacher-email'];
    $rate = $_POST['teacher-rate'];
    $kyori = $_POST['kyori'];

    // Server-side validation
    if ($rate <= 0) {
        echo "<div class='alert alert-danger'>Please enter a valid payment rate.</div>";
    } elseif ($kyori < 0) {
        echo "<div class='alert alert-danger'>Distance cannot be negative.</div>";
    } else {
        // Use prepared statements for security
        $stmt = $conn->prepare("INSERT INTO Teacher (name, email, hourly_rate, kyori) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $name, $email, $rate, $kyori); // 's' for string, 'd' for double (decimal)

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Teacher added successfully!</div>";
            // Optionally redirect to another page to prevent resubmission
            // header("Location: success_page.php");
            // exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Add Teacher</h2>
        <form id="addTeacherForm" method="POST" action="">
            <div class="mb-3">
                <label for="teacher-name" class="form-label">Teacher Name</label>
                <input type="text" id="teacher-name" name="teacher-name" class="form-control" placeholder="Enter teacher's name" required>
            </div>
            <div class="mb-3">
                <label for="teacher-email" class="form-label">Email</label>
                <input type="email" id="teacher-email" name="teacher-email" class="form-control" placeholder="Enter teacher's email" required>
            </div>
            <div class="mb-3">
                <label for="teacher-rate" class="form-label">Payment Rate (Per Hour)</label>
                <input type="number" id="teacher-rate" name="teacher-rate" class="form-control" placeholder="Enter payment rate per hour" required>
            </div>
            <div class="mb-3">
                <label for="kyori" class="form-label">距離 (Distance)</label>
                <input type="number" id="kyori" name="kyori" class="form-control" placeholder="Enter distance" required>
            </div>
            <button type="submit" class="btn btn-primary" name="add_teacher">Add Teacher</button>
        </form>
    </div>

    <script>
        // JavaScript form validation
        document.getElementById('addTeacherForm').addEventListener('submit', function (e) {
            var email = document.getElementById('teacher-email').value;
            var rate = document.getElementById('teacher-rate').value;
            var kyori = document.getElementById('kyori').value;
            
            if (rate <= 0) {
                alert("Please enter a valid payment rate.");
                e.preventDefault();
            } else if (kyori < 0) {
                alert("Distance cannot be negative.");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
