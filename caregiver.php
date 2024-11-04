<?php
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "caregiver_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch caregiver details
$result = $conn->query("SELECT * FROM caregivers");

echo "<h1>Caregivers</h1>";
while ($row = $result->fetch_assoc()) {
    echo "<h2>Caregiver ID: " . $row['caregiver_id'] . "</h2>";
    echo "Available Hours: " . $row['available_hours'] . "<br>";
    echo "Rating: " . $row['rating'] . " stars<br>";
}
$conn->close();
?>
