<?php
$servername = "mysql311.phy.lolipop.lan";
$username = "LAA1516492";
$password = "1234";
$dbname = "LAA1516492-keziban";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
