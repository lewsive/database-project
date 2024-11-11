<?php
// Database connection parameters
$host = 'localhost'; // or your server address
$user = 'root'; // your database username
$password = ''; // your database password
$dbname = 'caregiver_db'; // your database name

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
