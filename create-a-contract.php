<?php
require 'dp.php';
session_start();

if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

$logged_in_phone = $_SESSION['phone_number'];
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDatabaseConnection();

    $start_date = htmlspecialchars(trim($_POST['start_date'] ?? ''));
    $end_date = htmlspecialchars(trim($_POST['end_date'] ?? ''));
    $weekly_hours = (int)($_POST['weekly_hours'] ?? 0);
    $rate = (float)($_POST['rate'] ?? 0);
    $caregiver_phone = htmlspecialchars(trim($_POST['caregiver_phone'] ?? ''));
    $receiver_phone = htmlspecialchars(trim($_POST['receiver_phone'] ?? ''));

    if (empty($start_date) || empty($end_date) || empty($weekly_hours) || empty($rate) || empty($caregiver_phone) || empty($receiver_phone)) {
        $error_message = "All fields are required.";
    } elseif ($start_date >= $end_date) {
        $error_message = "Start date must be before the end date.";
    } elseif ($weekly_hours <= 0) {
        $error_message = "Weekly hours must be greater than 0.";
    } elseif ($rate < 0) {
        $error_message = "Rate cannot be negative.";
    } else {
        try {
            // Validate caregiver hours
            $caregiverQuery = "SELECT TotalHours, HoursUsed FROM MEMBER WHERE PhoneNumber = :phone";
            $caregiverStmt = $pdo->prepare($caregiverQuery);
            $caregiverStmt->execute([':phone' => $caregiver_phone]);
            $caregiver = $caregiverStmt->fetch(PDO::FETCH_ASSOC);

            if (!$caregiver) {
                $error_message = "Caregiver not found.";
            } else {
                $totalHours = $caregiver['TotalHours'];
                $hoursUsed = $caregiver['HoursUsed'];
                $remainingHours = $totalHours - $hoursUsed;

                if ($weekly_hours > $remainingHours) {
                    $error_message = "The caregiver does not have enough available hours.";
                } else {
                    // Create contract
                    $query = "INSERT INTO CONTRACTS 
                                (StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber, InitiatorPhone, Status) 
                              VALUES 
                                (:start_date, :end_date, :weekly_hours, :rate, :caregiver_phone, :receiver_phone, :initiator_phone, 'Pending')";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([
                        ':start_date' => $start_date,
                        ':end_date' => $end_date,
                        ':weekly_hours' => $weekly_hours,
                        ':rate' => $rate,
                        ':caregiver_phone' => $caregiver_phone,
                        ':receiver_phone' => $receiver_phone,
                        ':initiator_phone' => $logged_in_phone,
                    ]);

                    // Update caregiver's hours
                    $updateQuery = "UPDATE MEMBER SET HoursUsed = HoursUsed + :weekly_hours WHERE PhoneNumber = :phone";
                    $updateStmt = $pdo->prepare($updateQuery);
                    $updateStmt->execute([':weekly_hours' => $weekly_hours, ':phone' => $caregiver_phone]);

                    header("Location: dashboard.php?message=Contract created and awaiting approval.");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $error_message = "Error creating contract: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contract</title>
    <style>
        /* Ensures the background covers the entire page */
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #FFD700 30%, #8A2BE2 70%);
            background-size: cover;
            background-attachment: fixed;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Vertically center the form */
            align-items: center;
            padding: 20px;
        }

        h1 {
            font-size: 2em;
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            color: black;
        }

        form {
            max-width: 500px;
            margin: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Slightly transparent background */
            padding: 20px;
            border-radius: 10px;
            width: 100%;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, button {
            padding: 10px;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4B0082; /* Dark Violet */
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #6A0DAD; /* Lighter Violet for hover effect */
        }

        a {
            color: white;
            text-decoration: none;
            display: block;
        }

        button a {
            color: white;
            text-decoration: none;
        }

        button a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            font-size: 0.9em;
        }

        /* Adding extra space between buttons */
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h1>Create a Contract</h1>
    <form action="create-a-contract.php" method="POST">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>

        <label for="weekly_hours">Weekly Hours:</label>
        <input type="number" id="weekly_hours" name="weekly_hours" min="1" required>

        <label for="rate">Rate (in $):</label>
        <input type="number" id="rate" name="rate" step="0.01" min="0" required>

        <label for="caregiver_phone">Caregiver Phone Number:</label>
        <input type="tel" id="caregiver_phone" name="caregiver_phone" required>

        <label for="receiver_phone">Care Receiver Phone Number:</label>
        <input type="tel" id="receiver_phone" name="receiver_phone" required>

        <?php if (!empty($error_message)): ?>
            <div style="color: red;"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <button type="submit">Submit Contract</button>
        <button><a href="dashboard.php">Back to Dashboard</a></button>
    </form>
</body>
</html>
