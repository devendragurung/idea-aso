<?php
header("Content-Type: text/html; charset=UTF-8");
require("p0.php");  // Database connection

if (isset($_GET['id'])) {
    $id = htmlspecialchars($_GET['id']);  // Sanitize input
} else {
    echo "<div class='alert alert-danger'>No lecture ID provided!</div>";
    exit;
}

try {
    // Delete the lecture record from the database
    $deleteQuery = "DELETE FROM Lecture WHERE id = ?";
    $stmt = $dbhost->prepare($deleteQuery);
    $stmt->execute([$id]);

    if ($stmt->rowCount()) {
        echo "<div class='alert alert-success'>Lecture deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-warning'>Lecture not found or could not be deleted!</div>";
    }

    // Redirect to home.php after 2 seconds
    header("Refresh: 2; url=home.php");
    exit;
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}
?>
