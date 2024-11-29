<?php
include('database.php');

$lectureAdded = false; // A flag to check if the lecture is added successfully

// Add lecture logic
if (isset($_POST['add_lecture'])) {
    $teacher_id = $_POST['teacher-id'];
    $subject = $_POST['lecture-subject'];
    $lecture_date = $_POST['lecture-date'];
    $start_time = $_POST['start-time'];
    $end_time = $_POST['end-time'];
    $bikou = $_POST['bikou'];
    $comasuu = $_POST['komasuu']; // Ensure the correct variable name

    // Calculate lecture hours
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = $start->diff($end);
    $lecture_hour = $interval->h + ($interval->i / 60);

    // Use prepared statements to avoid SQL injection
    $query = "INSERT INTO Lecture (teacher_id, subject, lecture_date, start_time, end_time, lecture_hour, bikou, comasuu) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssiss", $teacher_id, $subject, $lecture_date, $start_time, $end_time, $lecture_hour, $bikou, $comasuu);

    if ($stmt->execute()) {
        $lectureAdded = true; // Set flag to true when lecture is added
        // Redirect to clear POST data and avoid form resubmission on refresh
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
            <label for="teacher-select" class="form-label">TeacherID</label>
            <input type="int" id="select-teacher" name="teacher-id" class="form-control"placeholder="Enter your teacher-Id number" required>
        </div>
        <div class="mb-3">
            <label for="lecture-subject" class="form-label">科目</label>
            <input type="text" id="lecture-subject" name="lecture-subject" class="form-control" placeholder="Enter lecture subject" required>
        </div>
        <div class="mb-3">
            <label for="lecture-date" class="form-label">日付</label>
            <input type="date" id="lecture-date" name="lecture-date" class="form-control" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="start-time" class="form-label">開始時間</label>
                <input type="text" id="start-time" name="start-time" class="form-control" placeholder="Enter time in 24-hour format" required pattern="([01][0-9]|2[0-3]):[0-5][0-9]">
            </div>
            <div class="col-md-6 mb-3">
                <label for="end-time" class="form-label">終了時間</label>
                <input type="text" id="end-time"  name="end-time" class="form-control" placeholder="Enter time in 24-hour format" required pattern="([01][0-9]|2[0-3]):[0-5][0-9]">
            </div>
            <div class="col-md-6 mb-3">
                <label for="komasuu" class="form-label">コマ数</label>
                <input type="text" id="komasuu" name="komasuu" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="bikou" class="form-label">備考</label>
                <input type="text" id="bikou" name="bikou" class="form-control">
            </div>
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
    </script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
