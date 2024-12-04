<?php
function getDatabaseConnection() {
    $host = 'cop4710.cl8emey6ahan.us-east-2.rds.amazonaws.com'; // AWS RDS endpoint
    $dbname = 'caregiver'; // Database name
    $username = 'admin'; // Database username
    $password = 'COP4710!'; // Database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function getDatabaseMysqli() {
    $link = mysqli_connect(
        "cop4710.cl8emey6ahan.us-east-2.rds.amazonaws.com",
        "admin",
        "COP4710!",
        "caregiver");
 
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    return $link;
}
?>
