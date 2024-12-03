<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Information</title>
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

        /* Box to display the information */
        .info-box {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.5); /* Transparent background for contrast */
            color: white;
        }

        .info-box h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-item label {
            font-weight: bold;
        }

        .info-item span {
            margin-left: 10px;
            font-weight: normal;
        }

        /* Button styling for consistent design */
        button {
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-align: center;
        }

        button a {
            color: white;
            text-decoration: none;
        }

        button a:hover {
            text-decoration: underline;
        }

        /* Styling for the back button */
        .back-btn {
            background-color: #4B0082; /* Dark Violet */
        }

        .back-btn:hover {
            background-color: #6A0DAD; /* Lighter Violet */
        }
    </style>
</head>
<body>
    <div class="info-box">
        <h1>Member Information</h1>
        <div class="info-item">
            <label>Name:</label>
            <span id="memberName">John Doe</span>
        </div>
        <div class="info-item">
            <label>Remaining Available Hours per Week:</label>
            <span id="remainingHours">15</span>
        </div>
        <div class="info-item">
            <label>Review Rating:</label>
            <span id="reviewRating">4.5/5</span>
        </div>
        <div class="info-item">
            <label>Contact:</label>
            <span id="contact">123-456-7890</span>
        </div>
    </div>

    <button class="back-btn" type="button"><a href="dashboard.html">Back to Dashboard</a></button>

    <script>
        // Example data; replace with dynamic data fetching in a real application
        const memberData = {
            name: "John Doe",
            remainingHours: 15,
            reviewRating: 4.5,
            contact: "123-456-7890"
        };

        // Populate the page with member data
        document.getElementById("memberName").textContent = memberData.name;
        document.getElementById("remainingHours").textContent = memberData.remainingHours;
        document.getElementById("reviewRating").textContent = `${memberData.reviewRating}/5`;
        document.getElementById("contact").textContent = memberData.contact;
    </script>
</body>
</html>
