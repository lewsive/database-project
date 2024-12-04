<?php
require 'dp.php';
session_start();

if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

$pdo = getDatabaseConnection();
$logged_in_phone = $_SESSION['phone_number']; // Logged-in user's phone number
$error_message = "";

try {
    // Fetch contracts where the logged-in user is the caregiver or receiver, but NOT the initiator
    $query = "SELECT * FROM CONTRACTS 
              WHERE (CareGiverPhoneNumber = :phone OR CareRecieverPhoneNumber = :phone) 
              AND Status = 'Pending'";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':phone' => $logged_in_phone]);

    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter contracts: exclude contracts initiated by the logged-in user
    $contracts = array_filter($contracts, function ($contract) use ($logged_in_phone) {
        return $contract['CareGiverPhoneNumber'] === $logged_in_phone || $contract['CareRecieverPhoneNumber'] === $logged_in_phone;
    });
} catch (PDOException $e) {
    $error_message = "Error fetching pending contracts: " . $e->getMessage();
}

// Approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contract_id']) && isset($_POST['action'])) {
    $contract_id = $_POST['contract_id'];
    $action = $_POST['action'];

    try {
        // Fetch the contract to ensure it exists
        $checkQuery = "SELECT * FROM CONTRACTS WHERE ContractID = :contract_id";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([':contract_id' => $contract_id]);
        $contract = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) {
            $error_message = "Error: Contract not found.";
        } elseif ($contract['CareGiverPhoneNumber'] !== $logged_in_phone && $contract['CareRecieverPhoneNumber'] !== $logged_in_phone) {
            // Deny action if the logged-in user is not part of the contract
            $error_message = "You cannot approve or reject a contract you are not part of.";
        } else {
            // Update the status of the contract
            $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';
            $updateQuery = "UPDATE CONTRACTS SET Status = :status WHERE ContractID = :contract_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([':status' => $new_status, ':contract_id' => $contract_id]);

            // Redirect to refresh the page
            header("Location: pending.php?message=Contract $new_status successfully.");
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Error updating contract: " . $e->getMessage();
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
            background-color: rgba(0, 0, 0, 0.5);
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
            background-color: #4B0082;
            color: white;
        }

        .approve:hover {
            background-color: #6A0DAD;
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
    <h1>Pending Contracts</h1>
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
    <table border="1">
        <thead>
            <tr>
                <th>Contract ID</th>
                <th>Caregiver</th>
                <th>Care Receiver</th>
                <th>Hours</th>
                <th>Cost</th>
                <th>Action</th>
            </tr>
        </thead>
       <tbody>
    <?php if (count($contracts) > 0): ?>
        <?php foreach ($contracts as $contract): ?>
            <tr>
                <td><?= htmlspecialchars($contract['ContractID']) ?></td>
                <td><?= htmlspecialchars($contract['CareGiverPhoneNumber']) ?></td>
                <td><?= htmlspecialchars($contract['CareRecieverPhoneNumber']) ?></td>
                <td><?= htmlspecialchars($contract['WeeklyHours']) ?></td>
                <td>$<?= htmlspecialchars($contract['Rate']) ?></td>
                <td>
                    <?php if ($contract['InitiatorPhone'] === $logged_in_phone): ?>
                        <!-- Logged-in user is the initiator -->
                        <p>Pending</p>
                    <?php else: ?>
                        <!-- Logged-in user is not the initiator -->
                        <form action="pending.php" method="POST">
                            <input type="hidden" name="contract_id" value="<?= $contract['ContractID'] ?>">
                            <button type="submit" name="action" value="approve" class="approve">Approve</button>
                            <button type="submit" name="action" value="reject" class="reject">Reject</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6">No pending contracts.</td>
        </tr>
    <?php endif; ?>
</tbody>


    </table>
    <button><a href="dashboard.php">Back to Dashboard</a></button>
</body>
</html>
