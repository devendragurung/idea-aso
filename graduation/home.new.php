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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">IICA.JP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link text-white" href="timetable.php">時間割作成</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="add_lecture.php">実績登録</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="addteacher.php">講師を追加</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="invoice.php">請求書</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="login.php">ログイン</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person" style="color: white;"></i> <!-- Enhanced icon -->
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-item">
                                <div class="card" style="width: 250px;">
                                    <div class="text-center mt-3">
                                        <img src="user.png" alt="User Avatar" class="rounded-circle" style="width: 80px; height: 80px;">
                                    </div>
                                    <div class="card-body border-0">
                                        <h5 class="card-title text-center"><?php echo htmlspecialchars($user['name']); ?></h5>
                                        <p class="card-text text-center text-muted">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                                        <div class="d-grid gap-2">
                                            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center">時間割</h2>
    <div id="calendar"></div>
</div>

<!-- Add Lecture Modal -->
<div class="modal fade" id="addLectureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addLectureForm">
                <div class="modal-header">
                    <h5 class="modal-title">新しい講義を追加</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="lectureDate">
                    <div class="mb-3">
                        <label for="subject" class="form-label">科目</label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="teacherId" class="form-label">先生ID</label>
                        <input type="text" class="form-control" id="teacherId" required>
                    </div>
                    <div class="mb-3">
                        <label for="timeRange" class="form-label">時間帯</label>
                        <input type="text" class="form-control" id="timeRange" placeholder="例: 09:00-10:00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modals for Edit and Delete Lecture -->
<!-- Edit Lecture Modal -->
<div class="modal fade" id="editLectureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editLectureForm">
                <div class="modal-header">
                    <h5 class="modal-title">講義を編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editLectureId">
                    <div class="mb-3">
                        <label for="editSubject" class="form-label">科目</label>
                        <input type="text" class="form-control" id="editSubject" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTeacherId" class="form-label">先生ID</label>
                        <input type="text" class="form-control" id="editTeacherId" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTimeRange" class="form-label">時間帯</label>
                        <input type="text" class="form-control" id="editTimeRange" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Lecture Modal -->
<div class="modal fade" id="deleteLectureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">講義を削除</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteLectureId">
                本当にこの講義を削除しますか？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">削除</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var events = <?php echo json_encode($events); ?>;

    // Map subjects to colors
    var subjectColors = {
        "開発演習": "#FFD700", // Gold
        "ビジネス実務": "#87CEEB",    // Sky Blue
        "セキュリティ診断": "#FFA07A",    // Light Salmon
        "キャリア": "#98FB98",    // Pale Green
        "グローバルスタデ": "#DDA0DD"         // Plum
    };

    // Dynamically add colors for new subjects
    events.forEach(event => {
        if (!subjectColors[event.title]) {
            subjectColors[event.title] = '#' + Math.floor(Math.random() * 16777215).toString(16); // Random color
        }
    });

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ja',
        initialView: 'dayGridMonth',
        events: events.map(event => ({
            ...event,
            backgroundColor: subjectColors[event.title] || "#D3D3D3", // Default Light Gray
            borderColor: subjectColors[event.title] || "#D3D3D3"
        })),
        eventContent: function (info) {
            let teacherName = info.event.extendedProps.teacher;
            let timeRange = info.event.extendedProps.timeRange;
            let eventId = info.event.id;

            // Create custom HTML for event content
            let customHtml = `
                <div class="event-content">
                    <strong>${info.event.title}</strong><br>
                    <span>${teacherName}</span><br>
                    <span>${timeRange}</span><br>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${eventId}">編集</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${eventId}">削除</button>
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

    // Edit Lecture
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-btn')) {
            const id = e.target.dataset.id;
            const event = events.find(event => event.id == id);

            document.getElementById('editLectureId').value = id;
            document.getElementById('editSubject').value = event.title;
            document.getElementById('editTeacherId').value = event.teacherID;
            document.getElementById('editTimeRange').value = event.timeRange;

            new bootstrap.Modal(document.getElementById('editLectureModal')).show();
        }
    });

    document.getElementById('editLectureForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('editLectureId').value;
        const subject = document.getElementById('editSubject').value;
        const teacherID = document.getElementById('editTeacherId').value;
        const timeRange = document.getElementById('editTimeRange').value;

        fetch('edit_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, subject, teacherID, timeRange })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lecture updated successfully!');
                location.reload();
            } else {
                alert(`Error: ${data.error}`);
            }
        });
    });

    // Delete Lecture
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-btn')) {
            const id = e.target.dataset.id;
            document.getElementById('deleteLectureId').value = id;
            new bootstrap.Modal(document.getElementById('deleteLectureModal')).show();
        }
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        const id = document.getElementById('deleteLectureId').value;

        fetch('delete_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lecture deleted successfully!');
                location.reload();
            } else {
                alert(`Error: ${data.error}`);
            }
        });
    });

    // Add Lecture
    document.getElementById('addLectureForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const lectureDate = document.getElementById('lectureDate').value;
        const subject = document.getElementById('subject').value;
        const teacherID = document.getElementById('teacherId').value;
        const timeRange = document.getElementById('timeRange').value;

        fetch('add_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ date: lectureDate, subject, teacherID, timeRange }),
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('講義が正常に追加されました！');
                location.reload();
            } else {
                alert(`エラー: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。もう一度お試しください。');
        });
    });
});

</script>
</body>
</html>
