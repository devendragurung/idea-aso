<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>給与計算</title>
    <style>
        .salary-sheet {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #000;
            border-radius: 10px;
        }
        .salary-sheet h2 {
            margin-bottom: 20px;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script>
        // Function to print the salary sheet
        function printSalarySheet() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container mt-3">
        <?php
        // Include the database connection file
        include('database.php');

        // Get the teacher_id and month from the previous form submission
        $teacher_id = $_POST['teacher_id'] ?? null;
        $month = $_POST['month'] ?? null;

        // Initialize variables
        $total_salary = null;
        $total_hours = null;
        $hourly_rate = null;

        if ($teacher_id && $month) {
            // Extract year and month from the selected date
            $year = date("Y", strtotime($month));
            $month_number = date("m", strtotime($month));

            // Query to get hourly rate from Teacher table
            $stmt = $conn->prepare("SELECT hourly_rate FROM Teacher WHERE teacher_id = ?");
            $stmt->bind_param("s", $teacher_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $teacher_data = $result->fetch_assoc();
                $hourly_rate = $teacher_data['hourly_rate'];
                
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
                $total_hours = $lecture_data['total_hours'];

                // Calculate total salary
                $total_salary = $total_hours * $hourly_rate;
            } else {
                echo "<h5 class='text-danger'>Teacher data not found.</h5>";
            }
        } else {
            echo "<h5 class='text-danger'>Teacher ID or month not provided.</h5>";
        }

        // Show the salary sheet if total salary is calculated
        if (isset($total_salary)) {
        ?>
            <div class="salary-sheet" id="salary-content">
                <h2>給与シート</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>講師ID</th>
                            <th>合計勤務時間 (時間)</th>
                            <th>時給 (円)</th>
                            <th>合計給与 (円)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher_id); ?></td>
                            <td><?php echo htmlspecialchars($total_hours); ?></td>
                            <td><?php echo htmlspecialchars($hourly_rate); ?></td>
                            <td><?php echo htmlspecialchars(number_format($total_salary)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Print and Download buttons -->
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="printSalarySheet()">印刷</button>
                <form action="download_csv1.php" method="post" style="display:inline;">
                    <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher_id); ?>">
                    <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
                    <button type="submit" name="download_csv" class="btn btn-secondary">CSVをダウンロード</button>
                </form>
            </div>
        <?php
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
