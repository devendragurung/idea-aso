<?php
include 'database.php';

// Fetch timetable data from the database
$query = "SELECT lecture_date, time_range, subject_name FROM timetable ORDER BY lecture_date";
$stmt = $conn->prepare($query);
$stmt->execute();
$timetableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecture Timetable Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        .calendar-day {
            border: 1px solid #dee2e6;
            padding: 10px;
            min-height: 100px;
            position: relative;
        }
        .lecture-info {
            font-size: 0.85rem;
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 2px 5px;
            border-radius: 4px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="text-center mb-4">Lecture Timetable Calendar</h2>
    <div class="calendar">
        <?php
        $daysInMonth = date('t'); // Get number of days in the current month
        $currentYearMonth = date('Y-m-'); // Current year and month
        $timetableByDate = [];

        // Group timetable data by date for easy access
        foreach ($timetableData as $row) {
            $date = $row['lecture_date'];
            if (!isset($timetableByDate[$date])) {
                $timetableByDate[$date] = [];
            }
            $timetableByDate[$date][] = $row;
        }

        // Display days in the calendar
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentYearMonth . str_pad($day, 2, '0', STR_PAD_LEFT);
            echo '<div class="calendar-day">';
            echo "<strong>$day</strong>";

            // Show timetable if it exists for this date
            if (isset($timetableByDate[$date])) {
                foreach ($timetableByDate[$date] as $lecture) {
                    echo '<div class="lecture-info">';
                    echo '<strong>' . htmlspecialchars($lecture['subject_name']) . '</strong><br>';
                    echo '<small>' . htmlspecialchars($lecture['time_range']) . '</small>';
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
