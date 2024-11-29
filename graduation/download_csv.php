<?php
// download_csv.php

// Check if the form was submitted
// download_csv.php

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacherIDStart = intval($_POST['teacherIDStart']);
    $teacherIDEnd = intval($_POST['teacherIDEnd']);

    if ($teacherIDStart > 0 && $teacherIDEnd > 0 && $teacherIDStart <= $teacherIDEnd) {
        // Database connection details
        $servername = "mysql311.phy.lolipop.lan";
        $username = "LAA1516492";
        $password = "1234";
        $dbname = "LAA1516492-keziban"; // Replace with your actual database name

        // Create connection using MySQLi
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Use prepared statements to avoid SQL injection
        $stmt = $conn->prepare("SELECT teacher_id, name, email, payment_rate_per_hour FROM Teacher WHERE teacher_id BETWEEN ? AND ?");
        $stmt->bind_param("ii", $teacherIDStart, $teacherIDEnd);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Set headers to download the file
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="teacher_results.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Open output stream
            $output = fopen('php://output', 'w');

            // Write the column headers
            fputcsv($output, ['TeacherID', 'Name', 'Email', 'PaymentRatePerHour']);

            // Write the data rows
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [$row['teacher_id'], $row['name'], $row['email'], $row['payment_rate_per_hour']]);
            }

            // Close output stream
            fclose($output);
        } else {
            echo "No results found.";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid teacher ID range.";
    }
} else {
    echo "Invalid request.";
}

?>
