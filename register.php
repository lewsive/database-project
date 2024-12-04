<?php
require 'dp.php'; 
session_start();

$error_message = "";

// Initialize form input variables to prevent warnings
$username = "";
$password = "";
$address = "";
$phone = "";
$available_hours = 0;
$parent_address = '';
$parent1_name = '';
$parent2_name = '';

// Process the form only if it’s submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $available_hours = (int)($_POST['available_hours'] ?? 0);
    $parent_address = htmlspecialchars(trim($_POST['parent_address'] ?? ''));
    $parent1_name = htmlspecialchars(trim($_POST['parent1_name'] ?? ''));
    $parent2_name = htmlspecialchars(trim($_POST['parent2_name'] ?? ''));

    // Input validation
    if (empty($username) || empty($password) || empty($address) || empty($phone) || $available_hours <= 0 || empty($parent_address) || empty($parent1_name)) {
        $error_message = "All fields are required, and available hours must be greater than 0.";
    } else {
        try {
            // Hash the password for secure storage
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $pdo = getDatabaseConnection();

            // SQL query to insert data into the MEMBER table
            $query = "INSERT INTO MEMBER (PhoneNumber, Address, Password, TotalHours, HoursUsed, Balance, Rating, Username)
                      VALUES (:phone, :address, :password, :total_hours, :hours_used, :balance, :rating, :username)";
            $stmt = $pdo->prepare($query);

            // Execute the query with default values for optional fields
            $stmt->execute([
                ':phone' => $phone,
                ':address' => $address,
                ':password' => $hashed_password,
                ':total_hours' => $available_hours,
                ':hours_used' => 0,
                ':balance' => 2000.00, 
                ':rating' => 0, 
                ':username' => $username,
            ]);

            // SQL query to insert data into the PARENT table
            $query="INSERT INTO PARENT (ParentName1, ParentName2, Address, MemberPhoneNumber)
                    VALUES (:parent1_name, :parent2_name, :parent_address, :phone)";
            $stmt = $pdo->prepare($query);

            // Execute the query
            $stmt->execute([
                ':parent1_name' => $parent1_name,
                ':parent2_name' => $parent2_name,
                ':parent_address' => $parent_address,
                ':phone' => $phone,
            ]);

            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: Unable to register, please try again. " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Registration</title>
    <style>
        /* Full-page style */
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #FFD700 30%, #8A2BE2 70%);
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Registration container */
        .registration-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        /* Heading */
        h1 {
            margin-bottom: 20px;
            color: #4B0082; /* Dark Violet */
        }

        /* Form styling */
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        input[type="tel"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Submit button styling */
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4B0082; /* Dark Violet */
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #6A0DAD; /* Lighter Violet */
        }

        /* Link for login */
        p {
            font-size: 0.9em;
            color: #333;
        }

        a {
            color: #4B0082;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #6A0DAD;
        }
    </style>
</head>
<body>
<div class="registration-container">
        <h1>Register as a Caregiver</h1>
        <form action="register.php" method="POST"> 
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($address) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
            </div>

            <div class="form-group">
                <label for="available_hours">Available Hours per Week:</label>
                <input type="number" id="available_hours" name="available_hours" required>
            </div>

            <div class="form-group">
                <label for="parent1_name">1st Parent's Name:</label>
                <input type="text" id="parent1_name" name="parent1_name" required>
            </div>

            <div class="form-group">
                <label for="parent2_name">2nd Parent's Name:</label>
                <input type="text" id="parent2_name" name="parent2_name">
            </div>

            <div class="form-group">
                <label for="parent_address">Parent's Address:</label>
                <input type="text" id="parent_address" name="parent_address" required>
            </div>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <div class="error" style="color: red;"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

    </div>
</body>
</html>
