<?php
// Database credentials
$host = 'mysql309.phy.lolipop.lan';        // Usually 'localhost' or your server's IP address
$username = 'LAA1516492'; // Your database username
$password = '1234'; // Your database password
$dbname = 'LAA1516492-zikanwari';   // Your database name

// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
