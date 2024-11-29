<?php
include('database.php');

$lectureAdded = false; // A flag to check if the lecture is added successfully

// Add lecture logic
if (isset($_POST['add_lecture'])) {
    $teacher_id = $_POST['teacher_id'];
    $subject = $_POST['lecture-subject'];
    $lecture_date = $_POST['lecture-date'];
    $bikou = $_POST['bikou'];
    $comasuu = $_POST['komasuu']; // Ensure the correct variable name

    // Calculate lecture hours
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = $start->diff($end);
    $lecture_hour = $interval->h + ($interval->i / 60);

    // Handle selected time ranges (comma-separated list of levels)
    if (isset($_POST['time-id']) && is_array($_POST['time-id'])) {
        $selectedTimes = implode(', ', $_POST['time-id']); // Join selected time range labels into a string
    } else {
        $selectedTimes = ''; // Default if no time ranges are selected
    }

    // Use prepared statements to avoid SQL injection
    $query = "INSERT INTO Lecture (teacher_id, subject, lecture_date, start_time, end_time, lecture_hour, bikou, comasuu, time_range) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssisss", $teacher_id, $subject, $lecture_date, $start_time, $end_time, $lecture_hour, $bikou, $comasuu, $selectedTimes);

    if ($stmt->execute()) {
        $lectureAdded = true; // Set flag to true when lecture is added
        // Redirect to avoid form resubmission on refresh
        header("Location: add_lecture.php?success=true");
        exit(); // Stop further script execution after redirect
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    
    $stmt->close(); // Close the prepared statement
    $conn->close(); // Close the connection
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lecture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f3f5;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            margin-top: 50px;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            font-weight: 700;
            color: #495057;
            text-align: center;
        }
        .btn-primary, .btn-secondary {
            padding: 10px 20px;
        }
        .alert-success {
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
        }
        .btn-light {
        background-color: transparent; /* Remove background color */
        color: #000; /* Change text color if needed */
        border-color: #ccc; /* Optional: Change border color */
    }

    .btn-light:hover {
        background-color: rgba(0, 0, 0, 0.1); /* Optional: Add a hover effect */
    }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class='alert alert-success text-center'>Lecture added successfully!</div>
            <div class='d-flex justify-content-between mt-3'>
                <a href='index.php' class='btn btn-secondary'>Go Back to Home</a>
                <a href='add_lecture.php' class='btn btn-primary'>Add Another Lecture</a>
            </div>
        <?php else: ?>
            <h2 class="mb-4">Add Lecture</h2>
            <form id="addLectureForm" method="POST" action="">
        <div class="mb-3">
            <label for="teacher-select" class="form-label">Select Teacher</label>
            <select id="teacher-select" name="teacher_id" class="form-select" required>
                <?php
                $query = "SELECT * FROM Teacher";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['teacher_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="lecture-subject" class="form-label">科目</label>
            <input type="text" id="lecture-subject" name="lecture-subject" class="form-control" placeholder="Enter lecture subject" required>
        </div>
        <div class="mb-3">
            <label for="lecture-date" class="form-label">日付</label>
            <input type="date" id="lecture-date" name="lecture-date" class="form-control" required>
        </div>

            <div class="mb-3">
    <label class="form-label">Select Timeranges</label>
    <div>
        <?php
        // Query to fetch time ranges from the database
        $query = "SELECT * FROM subject"; // Adjust table name as needed
        $result = $conn->query($query);
        
        // Associative array for mapping time ranges to levels
        $levels = [
            1 => "１限",
            2 => "２限",
            3 => "３限",
            4 => "４限",
        ];
        
        // Fetching and displaying time ranges with corresponding levels
        while ($row = $result->fetch_assoc()) {
            $timeRange = htmlspecialchars($row['time_range']);
            $level = isset($levels[$row['id']]) ? $levels[$row['id']] : "Level Not Assigned"; // Get the level label
            
            // Create a checkbox for each time range with the label as the value
            echo "<div class='form-check'>";
            echo "<input class='form-check-input' type='checkbox' name='time-id[]' value='" . htmlspecialchars($level) . "' id='time-" . $row['id'] . "' onclick='updateLevelInput()'>";
            echo "<label class='form-check-label' for='time-" . $row['id'] . "'>" . $level . " (" . $timeRange . ")</label>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<div class="mb-3">
    <label for="level-input" class="form-label">Level of Selected Timeranges</label>
    <input type="text" id="level-input" name="time-range" class="form-control" readonly>
</div>
<div class="col-md-12 mb-3"> <!-- Change from col-md-6 to col-md-12 -->
    <label for="komasuu" class="form-label">コマ数</label>
    <input type="text" id="komasuu" name="komasuu" class="form-control" readonly>
</div>
<div class="col-md-12 mb-3"> <!-- Change from col-md-6 to col-md-12 -->
    <label for="bikou" class="form-label">備考</label>
    <input type="text" id="bikou" name="bikou" class="form-control">
</div>
        <button type="submit" class="btn btn-primary w-100" name="add_lecture">Add Lecture</button>
    </form>

        <?php endif; ?>
    </div>

    <script>
    // JavaScript validation for lecture time
    document.getElementById('addLectureForm').addEventListener('submit', function (e) {
        var startTime = document.getElementById('start-time').value;
        var endTime = document.getElementById('end-time').value;

        if (startTime >= endTime) {
            alert("End time must be after the start time.");
            e.preventDefault();
        }
    });

    function updateLevelInput() {
        // Get all checked checkboxes
        const checkboxes = document.querySelectorAll('input[name="time-id[]"]:checked');
        const levels = [];
        
        // Count the number of checked boxes
        let count = checkboxes.length;
        
        checkboxes.forEach((checkbox) => {
            // Get the level from the label associated with the checkbox
            const label = document.querySelector(`label[for='${checkbox.id}']`).textContent;
            levels.push(label.split(' ')[0]); // Assuming the level is the first word
        });
        
        // Update the input field with the selected levels
        document.getElementById('level-input').value = levels.join(', '); // Join levels with a comma
        
        // Update the comasuu input field with the count of selected time ranges
        document.getElementById('komasuu').value = count; // Update comasuu value
    }
</script>


    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
