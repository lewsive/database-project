<?php
function getDatabaseConnection() {
    $host = ''; // AWS RDS endpoint
    $dbname = ''; // Database name
    $username = ''; // Database username
    $password = ''; // Database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
