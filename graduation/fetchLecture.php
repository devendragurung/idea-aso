<?php
// Include database connection
include 'dbConnection.php';

// Prepare the response array for the lectures
$lectures = [];

// Query to fetch lectures from the Timetable table
$sql = "SELECT subject_id, lecture_date, time_range FROM Timetable";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Each lecture will be added as an event in FullCalendar format
        $lectures[] = [
            'title' => $row['subject_id'],  // Assuming 'subject_id' refers to a subject name or code
            'start' => $row['lecture_date'] . 'T' . $row['time_range'],
            'allDay' => false  // Set to true if it's a full-day event
        ];
    }
}

// Output the lectures in JSON format for FullCalendar
echo json_encode($lectures);
?>
