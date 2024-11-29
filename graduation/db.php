<?php
// Database connection using PDO
$host = 'mysql311.phy.lolipop.lan';          // or your DB host
$dbname = 'LAA1516492-keziban';    // your database name
$username = 'LAA1516492';           // your DB username
$password = '1234';               // your DB password

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
