<?php 
require 'dp.php';
session_start();

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    if (empty($username) || empty($password)) {
        $error_message = "Both fields must be filled.";
    } else {
        try {
            $pdo = getDatabaseConnection();

            // Fetch Password and PhoneNumber for the given username
            $query = "SELECT Password, PhoneNumber FROM MEMBER WHERE Username = :username";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Password'])) {
                // Store username and phone number in session
                $_SESSION['username'] = $username;
                $_SESSION['phone_number'] = $user['PhoneNumber'];

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username and password combination.";
            }
        } catch (PDOException $e) {
            $error_message = "Error: Unable to process login. " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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

        /* Login container */
        .login-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Heading */
        h2 {
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

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Submit button styling */
        button {
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

        button:hover {
            background-color: #6A0DAD; /* Lighter Violet */
        }

        /* Link for registration */
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
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <div style="color: red;"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

    </div>
</body>
</html>
