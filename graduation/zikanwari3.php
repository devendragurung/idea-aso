<?php
// Include the database connection file
include 'db_connection.php';

// Fetch timetable events
$sql = "SELECT id, subject, name, teacher_id, lecture_date, time_range FROM Lecture INNER JOIN Teacher USING (teacher_id);";
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['subject'],
            'teacher' => $row['name'],
            'teacherID' => $row['teacher_id'],
            'timeRange' => $row['time_range'],
            'date' => $row['lecture_date']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>時間割カレンダー</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/ja.js"></script>
    <style>
        #calendar { max-width: 1000px; margin: auto; }
        .event-content { white-space: normal; }
        .fc-daygrid-event { cursor: pointer; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">時間割</h2>
    <div id="calendar"></div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const events = <?php echo json_encode($events); ?>;

        // Map subjects to colors
        const subjectColors = {
            "開発演習": "#FFD700", // Gold
            "ビジネス実務": "#87CEEB", // Sky Blue
            "セキュリティ診断": "#FFA07A", // Light Salmon
            "キャリア": "#98FB98", // Pale Green
            "グローバルスタデ": "#DDA0DD" // Plum
        };

        // Assign random colors for new subjects
        events.forEach(event => {
            if (!subjectColors[event.title]) {
                subjectColors[event.title] = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
            }
        });

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'ja',
            initialView: 'dayGridMonth',
            events: events.map(event => ({
                ...event,
                backgroundColor: subjectColors[event.title] || "#D3D3D3",
                borderColor: subjectColors[event.title] || "#D3D3D3"
            })),
            eventContent: function (info) {
                const teacherName = info.event.extendedProps.teacher || 'Unknown';
                const timeRange = info.event.extendedProps.timeRange || 'N/A';
                const eventId = info.event.id;

                // Custom HTML for events
                const customHtml = `
                    <div class="event-content">
                        <strong>${info.event.title}</strong><br>
                        <span>${teacherName}</span><br>
                        <span>${timeRange}</span><br>
                    </div>
                `;
                return { html: customHtml };
            },
            dateClick: function (info) {
                document.getElementById('lectureDate').value = info.dateStr;
                new bootstrap.Modal(document.getElementById('addLectureModal')).show();
            }
        });

        calendar.render();
    });
</script>
</body>
</html>
