<?php
// Include the database connection file
include('database.php');

// Get the teacher_id and month from the form submission
$teacher_id = $_POST['teacher_id'] ?? null;
$month = $_POST['month'] ?? null;

if ($teacher_id && $month) {
    // Extract year and month from the selected date
    $year = date("Y", strtotime($month));
    $month_number = date("m", strtotime($month));

    // Query to get hourly rate from Teacher table
    $stmt = $conn->prepare("SELECT hourly_rate, name FROM Teacher WHERE teacher_id = ?");
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $teacher_data = $result->fetch_assoc();
        $hourly_rate = $teacher_data['hourly_rate'];
        $teacher_name = $teacher_data['name'];

        // Query to sum the total working hours (comasuu) from Lecture table for the selected teacher and month
        $stmt = $conn->prepare("
            SELECT SUM(comasuu) AS total_hours 
            FROM Lecture 
            WHERE teacher_id = ? 
            AND YEAR(lecture_date) = ? 
            AND MONTH(lecture_date) = ?
        ");
        $stmt->bind_param("sss", $teacher_id, $year, $month_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $lecture_data = $result->fetch_assoc();
        $total_hours = $lecture_data['total_hours'] ?? 0; // Handle null case

        // Calculate total salary
        $total_salary = $total_hours * $hourly_rate;

        // Output HTML with UTF-8 encoding and Bootstrap 5 for design
        echo "
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <title>Salary Sheet</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/jspdf-autotable'></script>
    <script src='notoSerifFont.js'></script> <!-- Include your custom font -->
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='card'>
            <div class='card-header text-center'>
                <h3>請求書</h3>
            </div>
            <div class='card-body'>
                <table class='table table-bordered'>
                    <tr>
                        <th class='w-50'>Teacher ID</th>
                        <td>$teacher_id</td>
                    </tr>
                    <tr>
                        <th>Teacher Name</th>
                        <td>$teacher_name</td>
                    </tr>
                    <tr>
                        <th>Month</th>
                        <td>$month</td>
                    </tr>
                    <tr>
                        <th>Total Hours Worked</th>
                        <td>$total_hours hours</td>
                    </tr>
                    <tr>
                        <th>Hourly Rate</th>
                        <td>$hourly_rate yen</td>
                    </tr>
                    <tr>
                        <th>Total Salary</th>
                        <td>" . number_format($total_salary) . " yen</td>
                    </tr>
                </table>
            </div>
            <div class='card-footer text-center'>
                <button class='btn btn-primary' id='download-btn'>Download as PDF</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('download-btn').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Add the Noto Serif Variable font
            doc.addFileToVFS('NotoSerifJP-VariableFont_wght.ttf', notoSerifBase64); // Update with your variable name
            doc.addFont('NotoSerifJP-VariableFont_wght.ttf', 'NotoSerifJP', 'normal');
            doc.setFont('NotoSerifJP', 'normal'); // Use the new font
            doc.setFontSize(16);

            // Add Title
            doc.text('給与明細書 (Salary Sheet)', 10, 10);

            // Table content (vertical style for caption and data)
            doc.autoTable({
                head: [['Field', 'Value']],
                body: [
                    ['Teacher ID', '$teacher_id'],
                    ['Teacher Name', '$teacher_name'],
                    ['Month', '$month'],
                    ['Total Hours Worked', '$total_hours hours'],
                    ['Hourly Rate', '$hourly_rate yen'],
                    ['Total Salary', '" . number_format($total_salary) . " yen']
                ],
                startY: 20,
                styles: {
                    font: 'NotoSerifJP', // Ensure this matches the name given in addFont
                    fontSize: 12
                }
            });

            // Save the PDF
            doc.save('salary_sheet.pdf');
        });
    </script>
</body>
</html>

        ";
    } else {
        echo "Teacher data not found.";
    }
} else {
    echo "Teacher ID or month not provided.";
}

// Close the database connection
$conn->close();
?>
