<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: home.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #ffd700 30%, #8a2be2 70%);
        color: white;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
      }

      h1 {
        font-size: 3em;
        text-align: center;
        margin-bottom: 30px;
      }

      form {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 50px;
      }

      label {
        font-size: 1.2em;
        margin-bottom: 10px;
      }

      input[type="text"] {
        padding: 10px;
        font-size: 1.2em;
        width: 300px;
        margin-bottom: 20px;
        border: 2px solid #fff;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
      }

      button {
        background-color: #4b0082; /* Dark Violet */
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 1.2em;
        cursor: pointer;
        margin-bottom: 10px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
      }

      button:hover {
        background-color: #6a0dad; /* Lighter Violet for hover effect */
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

      .button-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }
    </style>
  </head>
  <body>
    <h1>Search the Caregiver Community:</h1>

    <form action="search_results.php" method="GET">
      <label for="search">Search:</label>
      <input
        type="text"
        id="search"
        name="query"
        placeholder="Enter your search..."
        required
      />
      <button type="submit">Search</button>
    </form>

    <div class="button-container">
      <button><a href="profile.php">View Your Profile</a></button>
      <button><a href="create-a-contract.php">Create a Contract</a></button>
      <button><a href="contract.php">Contracts</a></button>
      <button>
        <a href="pending.php">Pending Contracts/Pending Reviews</a>
      </button>
    </div>
  </body>
</html>
