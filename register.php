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
        <form action="dashboard.html" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="available_hours">Available Hours per Week:</label>
                <input type="number" id="available_hours" name="available_hours" required>
            </div>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.html">Login</a></p>
    </div>
</body>
</html>
