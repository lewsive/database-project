<?php
require 'dp.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

$error_message = "";
$success_message = "";

// Fetch data for the logged-in user
$logged_in_phone = $_SESSION['phone_number'];
$pdo = getDatabaseConnection();

try {
    // Fetch user's total hours, hours used, balance, and rating
    $query = "SELECT TotalHours, HoursUsed, Balance, Rating FROM MEMBER WHERE PhoneNumber = :phone";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':phone' => $logged_in_phone]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalHours = $data['TotalHours'];
    $hoursUsed = $data['HoursUsed'];
    $remainingHours = $totalHours - $hoursUsed;
    $balance = $data['Balance'];
    $rating = $data['Rating'];

    // Fetch only approved or denied contracts tied to the logged-in user's phone number, limited to 10
    $query = "SELECT ContractID, StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber, Status 
              FROM CONTRACTS 
              WHERE Status IN ('Approved', 'Denied') 
              AND (CareGiverPhoneNumber = :phone OR CareRecieverPhoneNumber = :phone)
              ORDER BY ContractID DESC 
              LIMIT 10";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':phone' => $logged_in_phone]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Error fetching profile information: " . $e->getMessage();
}

// Handle profile updates (e.g., bio)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    $bio = htmlspecialchars(trim($_POST['bio'] ?? ''));

    // Limit the bio length to 500 characters
    if (strlen($bio) > 500) {
        $error_message = "Bio cannot exceed 500 characters.";
    } else {
        try {
            $query = "UPDATE MEMBER SET Bio = :bio WHERE PhoneNumber = :phone";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':bio' => $bio, ':phone' => $logged_in_phone]);
            $success_message = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
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
            align-items: flex-start;
            padding-left: 20px;
        }
        h1, h2 {
            color: #4B0082; /* Dark Violet */
        }
        .section {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 10px 20px;
            background-color: #4B0082;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #6A0DAD;
        }
        button a {
            color: white;
            text-decoration: none;
        }
        button a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Your Profile</h1>

    <?php if (!empty($error_message)): ?>
        <div style="color: red;"><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div style="color: green;"><?= htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Remaining Hours Section -->
    <div class="section">
        <h2>Remaining Hours</h2>
        <p>Your remaining service hours this week: <strong><?= htmlspecialchars($remainingHours) ?></strong></p>
    </div>

    <!-- Care Money Balance Section -->
    <div class="section">
        <h2>Care Money Balance</h2>
        <p>Your current balance is: <strong>$<?= htmlspecialchars($balance) ?></strong></p>
    </div>

    <!-- Average Review Scores Section -->
    <div class="section">
        <h2>Average Review Score</h2>
        <p>Your average review score is: <strong><?= htmlspecialchars($rating) ?>/5</strong></p>
    </div>

    <!-- Approved or Denied Contracts Section -->
    <div class="section">
        <h2>Recent Contracts</h2>
        <table>
            <thead>
                <tr>
                    <th>Contract ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Weekly Hours</th>
                    <th>Rate</th>
                    <th>Caregiver Phone</th>
                    <th>Care Receiver Phone</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contracts)): ?>
                    <?php foreach ($contracts as $contract): ?>
                        <tr>
                            <td><?= htmlspecialchars($contract['ContractID']) ?></td>
                            <td><?= htmlspecialchars($contract['StartDate']) ?></td>
                            <td><?= htmlspecialchars($contract['EndDate']) ?></td>
                            <td><?= htmlspecialchars($contract['WeeklyHours']) ?></td>
                            <td>$<?= htmlspecialchars($contract['Rate']) ?></td>
                            <td><?= htmlspecialchars($contract['CareGiverPhoneNumber']) ?></td>
                            <td><?= htmlspecialchars($contract['CareRecieverPhoneNumber']) ?></td>
                            <td><?= htmlspecialchars($contract['Status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No approved or denied contracts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Profile Update Section -->
    <div class="section">
        <h2>Update Profile</h2>
        <form action="profile.php" method="POST">
            <label for="bio">Update Bio:</label>
            <textarea id="bio" name="bio" placeholder="Write something about yourself..." maxlength="500"></textarea>
            <button type="submit">Update Profile</button>
        </form>
    </div>

    <!-- Back and Sign Out Buttons -->
    <button><a href="dashboard.php">Back to Dashboard</a></button>
    <button type="button"><a href="home.html">Sign Out</a></button>
</body>
</html>
