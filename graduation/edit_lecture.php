<?php
header("Content-Type: text/html; charset=UTF-8");
require("p0.php");  // Include database connection

if (isset($_GET['id'])) {
    $id = htmlspecialchars($_GET['id']);  // Sanitize input
} else {
    echo "No lecture ID provided!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form submission handling
    $subject = htmlspecialchars($_POST['subject']);
    $lectureDate = htmlspecialchars($_POST['lecture_date']);
    $comasuu = htmlspecialchars($_POST['comasuu']);
    $bikou = htmlspecialchars($_POST['bikou']);

    try {
        // Update query
        $updateQuery = "UPDATE Lecture SET subject = ?, lecture_date = ?, comasuu = ?, bikou = ? WHERE id = ?";
        $stmt = $dbhost->prepare($updateQuery);
        $stmt->execute([$subject, $lectureDate, $comasuu, $bikou, $id]);

        echo "<div class='alert alert-success'>Lecture updated successfully!</div>";
        header("Refresh: 2; url=lecture_list.php");  // Redirect to the lecture list after 2 seconds
        exit;
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error updating lecture: " . $e->getMessage() . "</div>";
    }
}

// Fetch lecture data
try {
    $query = "SELECT * FROM Lecture WHERE id = ?";
    $stmt = $dbhost->prepare($query);
    $stmt->execute([$id]);
    $lecture = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lecture) {
        echo "<div class='alert alert-danger'>Lecture not found!</div>";
        exit;
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lecture</title>
    <!-- Include Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Edit Lecture</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="subject" class="form-label">科目</label>
            <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($lecture['subject']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="lecture_date" class="form-label">日付</label>
            <input type="date" id="lecture_date" name="lecture_date" class="form-control" value="<?= htmlspecialchars($lecture['lecture_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="comasuu" class="form-label">コーマ数</label>
            <input type="text" id="comasuu" name="comasuu" class="form-control" value="<?= htmlspecialchars($lecture['comasuu']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="bikou" class="form-label">備考</label>
            <input type="text" id="bikou" name="bikou" class="form-control" value="<?= htmlspecialchars($lecture['bikou']); ?>">
        </div>
        <button type="submit" class="btn btn-success">Update Lecture</button>
        <a href="lecture_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
