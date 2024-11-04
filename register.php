<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root"; // Change if necessary
    $password = ""; // Change if necessary
    $dbname = "caregiver_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $available_hours = $_POST['available_hours'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (username, password, address, phone, available_hours) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $username, $password, $address, $phone, $available_hours);

    // Execute and check for errors
    if ($stmt->execute()) {
        // Redirect to home page if successful
        header("Location: home.php"); // Change to your actual home page
        exit(); // Ensure no further code is executed after redirection
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
