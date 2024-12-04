<?php
require 'dp.php';
session_start();

if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

$pdo = getDatabaseConnection();
$logged_in_phone = $_SESSION['phone_number'];
$error_message = "";
$success_message = "";

// Automatically mark contracts as completed and refund weekly hours to caregivers
try {
    $query = "SELECT ContractID, WeeklyHours, CareGiverPhoneNumber 
              FROM CONTRACTS 
              WHERE EndDate < CURDATE() AND IsCompleted = 0";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $contractsToUpdate = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($contractsToUpdate as $contract) {
        $contractID = $contract['ContractID'];
        $weeklyHours = $contract['WeeklyHours'];
        $careGiverPhone = $contract['CareGiverPhoneNumber'];

        // Mark the contract as completed
        $updateContractQuery = "UPDATE CONTRACTS SET IsCompleted = 1 WHERE ContractID = :contractID";
        $updateContractStmt = $pdo->prepare($updateContractQuery);
        $updateContractStmt->execute([':contractID' => $contractID]);

        // Refund weekly hours to the caregiver
        $updateHoursQuery = "UPDATE MEMBER SET HoursUsed = HoursUsed - :weeklyHours WHERE PhoneNumber = :careGiverPhone";
        $updateHoursStmt = $pdo->prepare($updateHoursQuery);
        $updateHoursStmt->execute([':weeklyHours' => $weeklyHours, ':careGiverPhone' => $careGiverPhone]);
    }
} catch (PDOException $e) {
    $error_message = "Error updating completed contracts: " . $e->getMessage();
}

// Fetch contracts where the logged-in user is involved and pending approval
try {
    $query = "SELECT ContractID, StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber, Status
              FROM CONTRACTS
              WHERE (CareGiverPhoneNumber = :phone OR CareRecieverPhoneNumber = :phone)
              AND Status = 'Pending'";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':phone' => $logged_in_phone]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching pending contracts: " . $e->getMessage();
}

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contract_id']) && isset($_POST['action'])) {
    $contract_id = $_POST['contract_id'];
    $action = $_POST['action'];

    try {
        // Fetch contract details
        $query = "SELECT StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber 
                  FROM CONTRACTS WHERE ContractID = :contract_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':contract_id' => $contract_id]);
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) {
            $error_message = "Contract not found.";
        } else {
            $caregiverPhone = $contract['CareGiverPhoneNumber'];
            $careReceiverPhone = $contract['CareRecieverPhoneNumber'];

            if ($action === 'approve') {
                // Calculate total payment
                $startDate = new DateTime($contract['StartDate']);
                $endDate = new DateTime($contract['EndDate']);
                $interval = $startDate->diff($endDate);
                $weeks = ceil($interval->days / 7); // Total weeks
                $totalHours = $weeks * $contract['WeeklyHours'];
                $totalPayment = $totalHours * $contract['Rate'];

                // Check if care receiver has sufficient balance
                $balanceQuery = "SELECT Balance FROM MEMBER WHERE PhoneNumber = :phone";
                $balanceStmt = $pdo->prepare($balanceQuery);
                $balanceStmt->execute([':phone' => $careReceiverPhone]);
                $careReceiverBalance = $balanceStmt->fetchColumn();

                if ($careReceiverBalance < $totalPayment) {
                    $error_message = "Insufficient balance to approve the contract.";
                } else {
                    // Deduct from care receiver's balance
                    $deductQuery = "UPDATE MEMBER SET Balance = Balance - :totalPayment WHERE PhoneNumber = :phone";
                    $deductStmt = $pdo->prepare($deductQuery);
                    $deductStmt->execute([':totalPayment' => $totalPayment, ':phone' => $careReceiverPhone]);

                    // Add to caregiver's balance
                    $addQuery = "UPDATE MEMBER SET Balance = Balance + :totalPayment WHERE PhoneNumber = :phone";
                    $addStmt = $pdo->prepare($addQuery);
                    $addStmt->execute([':totalPayment' => $totalPayment, ':phone' => $caregiverPhone]);

                    // Update contract status to Approved
                    $updateQuery = "UPDATE CONTRACTS SET Status = 'Approved' WHERE ContractID = :contract_id";
                    $updateStmt = $pdo->prepare($updateQuery);
                    $updateStmt->execute([':contract_id' => $contract_id]);

                    $success_message = "Contract approved and payment processed.";
                }
            } elseif ($action === 'reject') {
                // Update contract status to Rejected
                $updateQuery = "UPDATE CONTRACTS SET Status = 'Rejected' WHERE ContractID = :contract_id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([':contract_id' => $contract_id]);

                $success_message = "Contract rejected.";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error processing contract: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Contracts</title>
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
            align-items: center;
            padding: 20px;
        }

        h1 {
            font-size: 2em;
            text-align: center;
            margin-top: 20px;
            color: black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: black;
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
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #6A0DAD;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            width: 80%;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Pending Contracts</h1>
    <?php if ($success_message): ?>
        <div class="message success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="message error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>Contract ID</th>
                <th>Caregiver</th>
                <th>Care Receiver</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Rate</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($contracts) > 0): ?>
                <?php foreach ($contracts as $contract): ?>
                    <tr>
                        <td><?= htmlspecialchars($contract['ContractID']) ?></td>
                        <td><?= htmlspecialchars($contract['CareGiverPhoneNumber']) ?></td>
                        <td><?= htmlspecialchars($contract['CareRecieverPhoneNumber']) ?></td>
                        <td><?= htmlspecialchars($contract['StartDate']) ?></td>
                        <td><?= htmlspecialchars($contract['EndDate']) ?></td>
                        <td>$<?= htmlspecialchars($contract['Rate']) ?></td>
                        <td>
                            <form action="pending.php" method="POST">
                                <input type="hidden" name="contract_id" value="<?= htmlspecialchars($contract['ContractID']) ?>">
                                <button type="submit" name="action" value="approve">Approve</button>
                                <button type="submit" name="action" value="reject">Reject</button>
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
</body>
</html>
