<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacherIDStart = intval($_POST['teacherIDStart']);
    $teacherIDEnd = intval($_POST['teacherIDEnd']);
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    // Validate ID range
    if ($teacherIDStart > 0 && $teacherIDEnd > 0 && $teacherIDStart <= $teacherIDEnd) {
        // Database connection details
        $servername = "mysql311.phy.lolipop.lan";
        $username = "LAA1516492";
        $password = "1234";
        $dbname = "LAA1516492-keziban"; // Replace with your actual database name

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            // Return error as JSON
            echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
            exit;
        }

        // Build SQL query
        $sql = "SELECT teacher_id, name, email, payment_rate_per_hour FROM Teacher WHERE teacher_id BETWEEN ? AND ?";
        if (!empty($name)) {
            $name = "%{$name}%"; // Wildcard search for the name
            $sql .= " AND name LIKE ?";
        }

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind parameters based on whether name is provided
        if (!empty($name)) {
            $stmt->bind_param("iis", $teacherIDStart, $teacherIDEnd, $name);
        } else {
            $stmt->bind_param("ii", $teacherIDStart, $teacherIDEnd);
        }

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch results
        $teachers = [];
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }

        // Check if any results were found
        if (count($teachers) > 0) {
            // Return success with the list of teachers
            echo json_encode(['success' => true, 'results' => $teachers]);
        } else {
            // No results found for the given search criteria
            echo json_encode(['success' => false, 'message' => 'No teachers found in the given range.']);
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        // Invalid teacher ID range
        echo json_encode(['success' => false, 'message' => 'Invalid teacher ID range.']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
