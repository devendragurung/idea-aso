<?php
// db_connect.php

$host = 'mysql311.phy.lolipop.lan'; // Your database host
$user = 'LAA1516492';      // Your database username
$pass = '1234';          // Your database password
$db_name = 'LAA1516492-keziban'; // Database name

// Create a connection
$conn = new mysqli($host, $user, $pass, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
