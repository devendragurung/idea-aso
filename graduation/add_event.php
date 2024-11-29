<?php
include 'db_connection.php'; // Include your database connection

// Decode JSON data sent from the client
$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (isset($data['date'], $data['subject'], $data['teacherID'], $data['timeRange'])) {
    $lecture_date = $conn->real_escape_string($data['date']);
    $subject_name = $conn->real_escape_string($data['subject']);
    $teacher_id = $conn->real_escape_string($data['teacherID']);
    $time_range = $conn->real_escape_string($data['timeRange']);

    // Insert data into the Lecture table
    $sql = "INSERT INTO Lecture (lecture_date, subject, teacher_id, time_range) 
            VALUES ('$lecture_date', '$subject_name', '$teacher_id', '$time_range')";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}

$conn->close();
?>
