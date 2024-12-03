<?php
require 'dp.php';

// Database connection
$pdo = getDatabaseConnection();

// Select the CONTACTS Table
$query = "SELECT ContractID, StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber, Status FROM CONTRACTS";
$stmt = $pdo->prepare($query); 
$stmt->execute(); 
// get all the rows
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Contracts</title>
    <style>
        /* Ensures the background covers the entire page */
        /* Ensures the background covers the entire page */
html, body {
    height: 100%;
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #FFD700 30%, #8A2BE2 70%);
    background-size: cover; /* Ensure the gradient covers the entire screen */
    background-attachment: fixed;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Spread content across the full height */
    padding: 20px;
}

h1 {
    font-size: 2em;
    text-align: left;
    margin-top: 20px;
    margin-bottom: 20px;
    color: black;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    color: black; /* For text inside the table */
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
    background-color: #4B0082; /* Dark Violet */
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 1.2em;
    cursor: pointer;
    margin-top: 20px;
    width: 300px;
    text-align: left;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #6A0DAD; /* Lighter Violet for hover effect */
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

    </style>
</head>
<body>
    <h1>All Contracts</h1>

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
        <tbody id="contractList">
            <!-- Check if there's any rows in the table -->
            <?php if (count($contracts) > 0): ?>
                <!-- Printing each row in the table -->
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
            <!-- Message if there isn't data -->
            <?php else: ?>
                <tr>
                    <td colspan="7">No contracts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button><a href="dashboard.html">Back to Dashboard</a></button>
</body>
</html>
