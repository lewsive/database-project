<?php
require 'dp.php';
session_start(); 

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: home.html");
    exit();
}

$error_message = "";

try {
    $pdo = getDatabaseConnection();

    // Fetch pending contracts
    $query = "SELECT ContractID, StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber, Status 
              FROM CONTRACTS 
              WHERE Status = 'Pending'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $pendingContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Error: Unable to retrieve pending contracts. " . $e->getMessage();
}

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['contract_id'])) {
    $contractID = $_POST['contract_id'];
    $action = $_POST['action'];

    try {
        // Determine the new status based on the action
        $newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';

        // Update the contract status in the database
        $updateQuery = "UPDATE CONTRACTS SET Status = :status WHERE ContractID = :contract_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([':status' => $newStatus, ':contract_id' => $contractID]);

        // Redirect to refresh the page
        header("Location: pending.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Error: Unable to update contract status. " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reviews/Contracts</title>
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
            justify-content: center;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.5); /* Transparent background for table */
            padding: 10px;
            border-radius: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            color: black;
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 1em;
        }

        .approve {
            background-color: #4B0082; /* Dark Violet */
            color: white;
        }

        .approve:hover {
            background-color: #6A0DAD; /* Lighter Violet */
        }

        .reject {
            background-color: #dc3545;
            color: white;
        }

        .reject:hover {
            background-color: #c82333;
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
    <h1>Pending Reviews/Contracts</h1>

    <?php if (!empty($error_message)): ?>
        <div style="color: red;"><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Contract ID</th>
                <th>Caregiver Phone</th>
                <th>Care Receiver Phone</th>
                <th>Hours</th>
                <th>Cost</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pendingContracts)): ?>
                <?php foreach ($pendingContracts as $contract): ?>
                    <tr>
                        <td><?= htmlspecialchars($contract['ContractID']) ?></td>
                        <td><?= htmlspecialchars($contract['CareGiverPhoneNumber']) ?></td>
                        <td><?= htmlspecialchars($contract['CareRecieverPhoneNumber']) ?></td>
                        <td><?= htmlspecialchars($contract['WeeklyHours']) ?></td>
                        <td>$<?= htmlspecialchars($contract['Rate']) ?></td>
                        <td><?= htmlspecialchars($contract['Status']) ?></td>
                        <td>
                            <form method="POST" action="pending.php">
                                <input type="hidden" name="contract_id" value="<?= htmlspecialchars($contract['ContractID']) ?>">
                                <button type="submit" name="action" value="approve" class="approve">Approve</button>
                                <button type="submit" name="action" value="reject" class="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No pending contracts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button><a href="dashboard.html">Back to Dashboard</a></button>
</body>
</html>

