<?php
require 'dp.php';
session_start();

if (!isset($_SESSION['phone_number'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$contractID = $data['contractID'] ?? null;
$rating = $data['rating'] ?? null;
$logged_in_phone = $_SESSION['phone_number'];

if (!$contractID || !$rating || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

try {
    $pdo = getDatabaseConnection();

    // Ensure the logged-in user is the care receiver for this contract
    $query = "SELECT * FROM CONTRACTS 
              WHERE ContractID = :contractID 
              AND CareRecieverPhoneNumber = :phone 
              AND IsCompleted = 1 
              AND RatingGiven IS NULL";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':contractID' => $contractID, ':phone' => $logged_in_phone]);
    $contract = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contract) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid contract']);
        exit();
    }

    // Update the contract with the rating
    $updateQuery = "UPDATE CONTRACTS SET RatingGiven = :rating WHERE ContractID = :contractID";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([':rating' => $rating, ':contractID' => $contractID]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
