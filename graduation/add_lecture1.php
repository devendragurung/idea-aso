<?php
// Include the database connection
include 'db_connection.php';

// Fetch subjects with time ranges based on selected date
if (isset($_POST['fetch_timetable'])) {
    $date = $_POST['date'];
    $stmt = $conn->prepare("SELECT CONCAT(subject_name, ' - ', time_range) AS subject_time_range FROM timetable WHERE lecture_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $options = [];
    while ($row = $result->fetch_assoc()) {
        $options[] = $row['subject_time_range'];
    }
    echo json_encode($options);
    exit;
}

// Save attendance
if (isset($_POST['save_attendance'])) {
    $date = $_POST['date'];
    $teacher_name = $_POST['teacher_name'];
    $subject_time_ranges = $_POST['subject_time_range'];

    $errors = [];
    foreach ($subject_time_ranges as $subject_time_range) {
        $stmt = $conn->prepare("INSERT INTO attendance (attendance_date, subject_time_range, teacher_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $date, $subject_time_range, $teacher_name);

        if (!$stmt->execute()) {
            if ($stmt->errno == 1062) {
                $errors[] = "Duplicate entry for $subject_time_range on $date by $teacher_name.";
            } else {
                $errors[] = "Error: " . $stmt->error;
            }
        }
    }

    if (empty($errors)) {
        echo "success";
    } else {
        echo implode("<br>", $errors);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Attendance Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Teacher Attendance Form</h2>

    <!-- Success Alert -->
    <div id="successAlert" class="alert alert-success d-none" role="alert">
        Attendance saved successfully!
    </div>

    <form id="attendanceForm" class="border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="date" class="form-label">Select Date</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="subject_time_range" class="form-label">Select Subject + Time Range</label>
            <div id="subjectOptions" class="form-check"></div>
        </div>
        
        <div class="mb-3">
            <label for="teacher_name" class="form-label">Teacher Name</label>
            <input type="text" id="teacher_name" name="teacher_name" class="form-control" required>
        </div>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmationModal">Save Attendance</button>
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Save</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to save this attendance record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSave">Yes, Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // Fetch timetable on date selection
        $('#date').on('change', function() {
            var date = $(this).val();
            $.ajax({
                url: '', // Current page
                type: 'POST',
                data: {fetch_timetable: true, date: date},
                success: function(response) {
                    var options = JSON.parse(response);
                    var optionsHtml = '';
                    options.forEach(function(option) {
                        optionsHtml += '<div class="form-check">' +
                                        '<input class="form-check-input" type="checkbox" name="subject_time_range[]" value="' + option + '">' +
                                        '<label class="form-check-label">' + option + '</label>' +
                                       '</div>';
                    });
                    $('#subjectOptions').html(optionsHtml);
                }
            });
        });

        // Confirm Save Attendance
        $('#confirmSave').on('click', function() {
            var formData = $('#attendanceForm').serialize() + '&save_attendance=true';
            $.ajax({
                url: '', // Current page
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response === "success") {
                        $('#successAlert').removeClass('d-none');
                        $('#attendanceForm')[0].reset();
                        $('#subjectOptions').html('');
                    } else {
                        alert(response);
                    }
                    $('#confirmationModal').modal('hide');
                }
            });
        });
    });
</script>

</body>
</html>
