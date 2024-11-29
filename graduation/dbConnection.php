<?php
// Database credentials
$host = '';        // Usually 'localhost' or your server's IP address
$username = ''; // Your database username
$password = ''; // Your database password
$dbname = '';   // Your database name

// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
