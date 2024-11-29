document.addEventListener('DOMContentLoaded', function () {
    // Initialize FullCalendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        dateClick: function(info) {
            openLectureForm(info.dateStr);
        },
        events: 'fetchLectures.php'  // Fetch existing lectures from the server
    });
    calendar.render();

    // Copy Previous Month Button
    document.getElementById('copyButton').addEventListener('click', function () {
        fetch('copyMonthData.php')
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                calendar.refetchEvents();  // Refresh the calendar to show new data
            });
    });
});

// Open Lecture Form Modal
function openLectureForm(date) {
    document.getElementById('lecture_date').value = date;
    loadOptions();  // Load options for subjects and teachers
    document.getElementById('lectureFormModal').style.display = 'block';
}

// Close Lecture Form Modal
function closeLectureForm() {
    document.getElementById('lectureFormModal').style.display = 'none';
}

// Load Options for Subjects and Teachers
function loadOptions() {
    fetch('getOptions.php')
        .then(response => response.json())
        .then(data => {
            const subjectSelect = document.getElementById('subject_id');
            const teacherSelect = document.getElementById('teacher_id');
            
            subjectSelect.innerHTML = data.subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            teacherSelect.innerHTML = data.teachers.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
        });
}

// Save Lecture
document.getElementById('lectureForm').addEventListener('submit', function (event) {
    event.preventDefault();
    
    const formData = new FormData(this);
    fetch('saveLecture.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        document.getElementById('lectureFormModal').style.display = 'none';
        location.reload();  // Refresh the calendar
    });
});
