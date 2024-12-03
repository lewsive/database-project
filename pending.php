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

        /* Adding extra space between buttons */
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h1>Pending Reviews/Contracts</h1>

    <table>
        <thead>
            <tr>
                <th>Contract ID</th>
                <th>Caregiver</th>
                <th>Care Receiver</th>
                <th>Hours</th>
                <th>Cost</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="contractList">
            <!-- Example pending contract -->
            <tr>
                <td>001</td>
                <td>John Doe</td>
                <td>Jane Smith</td>
                <td>20</td>
                <td>$400</td>
                <td>Pending</td>
                <td>
                    <button class="approve" onclick="approveContract(1)">Approve</button>
                    <button class="reject" onclick="rejectContract(1)">Reject</button>
                </td>
            </tr>
            <!-- Add more contracts dynamically -->
        </tbody>
    </table>

    <button type="button"><a href="dashboard.html">Back to Dashboard</a></button>

    <script>
        // Redirect function
        function redirectToDashboard(action, id) {
            alert(`Contract ${id} ${action}!`);
            window.location.href = "dashboard.html";
        }

        // Function to approve a contract
        function approveContract(id) {
            redirectToDashboard('approved', id);
        }

        // Function to reject a contract
        function rejectContract(id) {
            redirectToDashboard('rejected', id);
        }
    </script>
</body>
</html>
