<?php
// Include database connection
include 'dbConnection.php';

// Check if required fields are set in the POST request
if (isset($_POST['lecture_date'], $_POST['time_range'], $_POST['subject_id'], $_POST['teacher_id'])) {
    // Get data from the POST request
    $lecture_date = $_POST['lecture_date'];
    $time_range = $_POST['time_range'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];

    // Prepare the SQL query to insert data into the Timetable table
    $sql = "INSERT INTO Timetable (lecture_date, time_range, subject_id, teacher_id) 
            VALUES (?, ?, ?, ?)";

    // Initialize a prepared statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters to the statement
        mysqli_stmt_bind_param($stmt, 'ssii', $lecture_date, $time_range, $subject_id, $teacher_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Lecture saved successfully!";
        } else {
            echo "Error: Could not save lecture. " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare the SQL statement. " . mysqli_error($conn);
    }
} else {
    echo "Error: Missing required fields.";
}

// Close the database connection
mysqli_close($conn);
?>
