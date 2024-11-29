<?php
// Database connection using PDO
$host = '';          // or your DB host
$dbname = '';    // your database name
$username = '';           // your DB username
$password = '';               // your DB password

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
