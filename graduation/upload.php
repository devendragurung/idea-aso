<?php
// Database connection (reuse your existing connection file)
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        // Skip the first row if it contains headers
        fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Extract data from CSV (assume columns: subject, teacher, date, time range)
            $subject = $data[0];
            $teacher = $data[1];
            $lecture_date = $data[2];
            $time_range = $data[3];

            // Insert into timetable table
            $stmt = $conn->prepare("INSERT INTO timetable (subject_name, teacher_name, lecture_date, time_range) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $subject, $teacher, $lecture_date, $time_range);
            $stmt->execute();
        }
        fclose($handle);
        echo "<script>alert('CSV uploaded successfully!'); window.location.href='index.php';</script>";
    }
}
?>
