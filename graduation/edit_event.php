<?php
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'], $data['subject'], $data['teacherID'], $data['timeRange'])) {
    $id = $conn->real_escape_string($data['id']);
    $subject = $conn->real_escape_string($data['subject']);
    $teacher_id = $conn->real_escape_string($data['teacherID']);
    $time_range = $conn->real_escape_string($data['timeRange']);

    $sql = "UPDATE Lecture SET subject='$subject', teacher_id='$teacher_id', time_range='$time_range' WHERE id='$id'";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data.']);
}

$conn->close();
?>
