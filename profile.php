<?php
require 'dp.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: home.html");
    exit();
}

$error_message = "";
$success_message = "";
$balance = 0;
$averageReview = 0;
$recentContracts = [];

try {
  $pdo = getDatabaseConnection();

  // Fetch user details
  $query = "SELECT Balance, Rating FROM MEMBER WHERE Username = :username";
  $stmt = $pdo->prepare($query);
  $stmt->execute([':username' => $_SESSION['username']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      $balance = $user['Balance'];
      $averageReview = $user['Rating'];
  }

  // Fetch most recent contracts using JOIN
  $query = "SELECT c.ContractID, c.CareRecieverPhoneNumber, c.Status 
            FROM CONTRACTS c
            JOIN MEMBER m ON c.CareGiverPhoneNumber = m.PhoneNumber
            WHERE m.Username = :username
            ORDER BY c.ContractID DESC LIMIT 10";
  $stmt = $pdo->prepare($query);
  $stmt->execute([':username' => $_SESSION['username']]);
  $recentContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  $error_message = "Error fetching profile information: " . $e->getMessage();
}


// Handle profile updates (e.g., bio)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    $bio = htmlspecialchars(trim($_POST['bio'] ?? ''));

    try {
        $query = "UPDATE MEMBER SET Bio = :bio WHERE Username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':bio' => $bio, ':username' => $_SESSION['username']]);
        $success_message = "Profile updated successfully.";
    } catch (PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
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
        /* Same styles as in the original HTML */
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
        h1, p, table, button {
            margin: 10px 0;
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
            background-color: #333;
            border: none;
            color: white;
            cursor: pointer;
        }
        button a {
            color: white;
            text-decoration: none;
        }
        button:hover {
            background-color: #555;
        }
        footer {
            margin-top: auto;
            padding: 10px;
            text-align: center;
        }
        a {
            color: white;
            text-decoration: none;
        }
        button a {
            display: block;
            color: white;
            text-decoration: none;
            font-size: 1.5em;
        }
        button a:hover {
            text-decoration: underline;
        }
        .section {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            width: 100%;
        }
        .section h2 {
            font-size: 1.8em;
        }
        .section p {
            font-size: 1.2em;
        }
        input, textarea {
            padding: 10px;
            font-size: 1em;
            width: 100%;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
        }
        textarea {
            height: 100px;
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

    <!-- Care Money Balance Section -->
    <div class="section">
        <h2>Care Money Balance</h2>
        <p>Your current balance is: <strong>$<?= htmlspecialchars($balance) ?></strong></p>
    </div>

    <!-- Average Review Scores Section -->
    <div class="section">
        <h2>Average Review Score</h2>
        <p>Your average review score is: <strong><?= htmlspecialchars($averageReview) ?>/5</strong></p>
    </div>

    <!-- Most Recent Contracts Section -->
    <div class="section">
        <h2>Most Recent Contracts</h2>
        <table>
            <thead>
                <tr>
                    <th>Contract ID</th>
                    <th>Care Receiver</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentContracts)): ?>
                    <?php foreach ($recentContracts as $contract): ?>
                        <tr>
                            <td><?= htmlspecialchars($contract['ContractID']) ?></td>
                            <td><?= htmlspecialchars($contract['CareRecieverPhoneNumber']) ?></td>
                            <td><?= htmlspecialchars($contract['Status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No recent contracts found.</td>
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
            <textarea id="bio" name="bio" placeholder="Write something about yourself..."></textarea>
            <button type="submit">Update Profile</button>
        </form>
    </div>

    <!-- Back and Sign Out Buttons -->
    <button><a href="dashboard.html">Back to Dashboard</a></button>
    <button type="button"><a href="home.html">Sign Out</a></button>
</body>
</html>
