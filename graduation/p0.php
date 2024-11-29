<?php

header("Content-Type: text/html; charset=UTF-8");


$host = "mysql311.phy.lolipop.lan";
$dbname ="LAA1516492-keziban"; 
$user = "LAA1516492";
$password = "1234"; 

$dsn = "mysql:host=".$host.";dbname=".$dbname;

try {
    $dbhost = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
}
