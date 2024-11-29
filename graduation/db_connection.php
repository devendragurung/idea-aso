<?php
// Database connection settings
$servername = "mysql311.phy.lolipop.lan";
$username = "LAA1516492";
$password = "1234";
$dbname = "LAA1516492-keziban";

// Establishing a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
