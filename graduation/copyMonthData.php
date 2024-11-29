<?php
// Include database connection
include 'dbConnection.php';

// Check if required fields are set in the POST request
if (isset($_POST['source_year'], $_POST['source_month'])) {
    $source_year = (int)$_POST['source_year'];
    $source_month = (int)$_POST['source_month'];
    
    // Determine the target month and year
    $target_year = $source_year;
    $target_month = $source_month + 1;
    if ($target_month > 12) {
        $target_month = 1;
        $target_year++;
    }

    // Format source and target date range
    $source_start_date = "$source_year-$source_month-01";
    $source_end_date = date("Y-m-t", strtotime($source_start_date)); // Last day of source month
    
    // Fetch data from the Timetable table for the source month
    $sql = "SELECT lecture_date, time_range, subject_id, teacher_id FROM Timetable 
            WHERE lecture_date BETWEEN ? AND ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $source_start_date, $source_end_date);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Prepare insert query for the target month
        $insert_sql = "INSERT INTO Timetable (lecture_date, time_range, subject_id, teacher_id) 
                       VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);

        if ($insert_stmt) {
            $copied_count = 0;

            // Loop through each lecture and insert it into the target month
            while ($row = mysqli_fetch_assoc($result)) {
                $original_date = new DateTime($row['lecture_date']);
                $day = $original_date->format('d');
                $new_date = "$target_year-$target_month-$day";

                mysqli_stmt_bind_param($insert_stmt, 'ssii', $new_date, $row['time_range'], $row['subject_id'], $row['teacher_id']);
                if (mysqli_stmt_execute($insert_stmt)) {
                    $copied_count++;
                }
            }

            echo "$copied_count lectures copied to $target_month-$target_year successfully!";
        } else {
            echo "Error preparing insert statement: " . mysqli_error($conn);
        }

        mysqli_stmt_close($insert_stmt);
    } else {
        echo "Error preparing select statement: " . mysqli_error($conn);
    }

    // Close the statement and connection
    mysqli_stmt_close($stmt);
} else {
    echo "Error: Missing source year or month.";
}

mysqli_close($conn);
?>
