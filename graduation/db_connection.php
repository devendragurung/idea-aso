<?php
// Database connection settings
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Establishing a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
