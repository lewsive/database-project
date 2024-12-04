<?php
require 'dp.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php");
    exit();
}

$pdo = getDatabaseConnection();
$logged_in_phone = $_SESSION['phone_number']; // Get the logged-in user's phone number

$error_message = "";

// Fetch contracts associated with the logged-in user
try {
    $query = "SELECT ContractID, StartDate, EndDate, WeeklyHours, Rate, CareGiverPhoneNumber, CareRecieverPhoneNumber, Status, IsCompleted, RatingGiven
              FROM CONTRACTS 
              WHERE (CareGiverPhoneNumber = :phone OR CareRecieverPhoneNumber = :phone) 
              AND (Status = 'Approved' OR Status = 'Rejected')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':phone' => $logged_in_phone]);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching contracts: " . $e->getMessage();
}
?>
<?php
try {
    // Automatically mark contracts as completed if today's date is past the EndDate
    $query = "UPDATE CONTRACTS 
              SET IsCompleted = 1 
              WHERE EndDate < CURDATE() AND IsCompleted = 0";
    $pdo->exec($query);
} catch (PDOException $e) {
    $error_message = "Error updating contract statuses: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Contracts</title>
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
            justify-content: space-between;
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
            background-color: #4B0082;
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
            background-color: #6A0DAD;
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

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 300px;
        }

        .modal .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5em;
            cursor: pointer;
        }

        .stars {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin: 20px 0;
        }

        .star {
            font-size: 2em;
            cursor: pointer;
            color: gray;
        }

        .star.selected {
            color: gold;
        }
    </style>
</head>
<body>
    <h1>All Contracts</h1>
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>
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
            <th>Rating</th>
        </tr>
    </thead>
    <tbody id="contractList">
        <?php if (count($contracts) > 0): ?>
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
                    <td>
    <?php if ($contract['CareRecieverPhoneNumber'] === $logged_in_phone && $contract['IsCompleted'] && is_null($contract['RatingGiven'])): ?>
        <button onclick="openRatingModal(<?= htmlspecialchars($contract['ContractID']) ?>)">Rate</button>
    <?php elseif (!is_null($contract['RatingGiven'])): ?>
        Rated: <?= htmlspecialchars($contract['RatingGiven']) ?>/5
    <?php else: ?>
        Not yet completed
    <?php endif; ?>
</td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No contracts found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

    <button><a href="dashboard.php">Back to Dashboard</a></button>

    <!-- Modal -->
    <!-- Rating Modal -->
    <div id="ratingModal" class="modal">
    <div class="modal-content">
        <h3>Rate Contract</h3>
        <div id="starContainer" class="stars"></div>
        <button id="submitRating">Submit</button>
        <button onclick="closeRatingModal()">Cancel</button>
    </div>
</div>


    <script>
        const modal = document.getElementById('ratingModal');
        const closeModal = document.getElementById('closeModal');
        const rateButtons = document.querySelectorAll('.rate-button');
        const stars = document.querySelectorAll('.star');
        const submitRating = document.getElementById('submitRating');
        let selectedContractID = null;
let selectedRating = 0;

// Open the rating modal
function openRatingModal(contractID) {
    selectedContractID = contractID;
    selectedRating = 0;

    const modal = document.getElementById('ratingModal');
    const starContainer = document.getElementById('starContainer');

    // Clear previous stars
    starContainer.innerHTML = '';

    // Populate stars dynamically
    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('span');
        star.textContent = '⭐';
        star.style.cursor = 'pointer';
        star.style.fontSize = '24px';
        star.className = 'star';
        star.onclick = () => selectRating(i); // Attach click event
        starContainer.appendChild(star);
    }

    modal.style.display = 'flex'; // Show the modal
}

// Highlight the selected stars
function selectRating(rating) {
    selectedRating = rating;
    const stars = document.getElementById('starContainer').children;

    for (let i = 0; i < stars.length; i++) {
        stars[i].style.color = i < rating ? 'gold' : 'gray'; // Highlight stars
    }
}

// Close the rating modal
function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
}

// Handle rating submission
document.getElementById('submitRating').onclick = async function () {
    if (selectedRating === 0) {
        alert('Please select a rating.');
        return;
    }

    try {
        const response = await fetch('rate-contract.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contractID: selectedContractID,
                rating: selectedRating,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert('Rating submitted successfully.');
            location.reload(); // Reload the page to reflect changes
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Failed to submit rating.');
    }
};


    </script>
</body>
</html>
