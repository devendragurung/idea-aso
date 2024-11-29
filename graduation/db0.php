<?php
// db_connect.php

$host = ''; // Your database host
$user = '';      // Your database username
$pass = '';          // Your database password
$db_name = ''; // Database name

// Create a connection
$conn = new mysqli($host, $user, $pass, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
