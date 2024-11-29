<?php
// Include database connection
include 'dbConnection.php';

// Array to store the options
$options = [
    'subjects' => [],
    'teachers' => []
];

// Fetch subjects
$subject_sql = "SELECT id, name FROM Subjects";
$subject_result = mysqli_query($conn, $subject_sql);

if ($subject_result) {
    while ($row = mysqli_fetch_assoc($subject_result)) {
        $options['subjects'][] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
} else {
    echo "Error fetching subjects: " . mysqli_error($conn);
}

// Fetch teachers
$teacher_sql = "SELECT id, name FROM Teachers";
$teacher_result = mysqli_query($conn, $teacher_sql);

if ($teacher_result) {
    while ($row = mysqli_fetch_assoc($teacher_result)) {
        $options['teachers'][] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
} else {
    echo "Error fetching teachers: " . mysqli_error($conn);
}

// Close database connection
mysqli_close($conn);

// Return options as JSON
header('Content-Type: application/json');
echo json_encode($options);
?>
