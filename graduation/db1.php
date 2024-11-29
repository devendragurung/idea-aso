<?php
// Database connection parameters
$host = 'mysql311.phy.lolipop.lan'; // Replace with your host (e.g., 127.0.0.1)
$db   = 'LAA1516492-keziban'; // Replace with your database name
$user = 'LAA1516492'; // Replace with your database username
$pass = '1234'; // Replace with your database password
$charset = 'utf8mb4';

// Set up the DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options for PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Error mode set to exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch mode set to associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Disable emulation of prepared statements
];

try {
    // Create a new PDO instance
    $conn = new PDO($dsn, $user, $pass, $options);
    // echo "Connected successfully"; // Optional: uncomment for testing connection
} catch (\PDOException $e) {
    // Handle connection error
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
