<?php
// Include database connection file
include 'db_connection.php';

// Initialize variables to store error messages
$errors = [];
$username = $password = $address = $phone = $available_hours = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $username = trim($_POST['username']);
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    $password = trim($_POST['password']);
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    $address = trim($_POST['address']);
    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    $phone = trim($_POST['phone']);
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }

    $available_hours = trim($_POST['available_hours']);
    if (empty($available_hours) || !is_numeric($available_hours)) {
        $errors[] = "Available hours must be a valid number.";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare the SQL statement
        $sql = "INSERT INTO users (username, password, address, phone, available_hours) VALUES (?, ?, ?, ?, ?)";
        
        // Prepare statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("ssssi", $username, $hashed_password, $address, $phone, $available_hours);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Registration successful, redirect to the home page
                header("Location: http://localhost/database-project/home.html");
                exit();
            } else {
                $errors[] = "Error: Could not execute the query. Please try again later.";
            }

            // Close statement
            $stmt->close();
        } else {
            $errors[] = "Error: Could not prepare the SQL statement. Please try again later.";
        }
    }
}

// Close connection
$conn->close();
?>