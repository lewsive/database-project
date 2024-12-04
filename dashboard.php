<?php
require 'dp.php';
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

      label {
        font-size: 1.2em;
        margin-bottom: 10px;
      }

      .search-box{
        width: 600px;
        position: relative;
        display: inline-block;
        font-size: 14px;
      }
      .search-box input[type="text"]{
          height: 32px;
          padding: 5px 10px;
          border: 1px solid #CCCCCC;
          font-size: 1.2em;
          background-color: rgba(255, 255, 255, 0.2);
          color: white;
      }
      .result{
          position: absolute;        
          z-index: 999;
          top: 100%;
          left: 0;
      }
      .search-box input[type="text"], .result{
          width: 100%;
          box-sizing: border-box;
      }
      table {
        border-collapse: collapse;
        width:100%;
      }
      .result td, th{
        margin:0;
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script>
    $(document).ready(function(){
        $('.search-box input[type="text"]').on("keyup input", function(){
            /* Get input value on change */
            var inputVal = $(this).val();
            var resultDropdown = $(this).siblings(".result");
            if(inputVal.length){
                $.get("livesearch.php", {term: inputVal}).done(function(data){
                    // Display the returned data in browser
                    resultDropdown.html(data);
                });
            } else{
                resultDropdown.empty();
            }
        });
        
        // Set search input value on click of result item
        $(document).on("click", ".result p", function(){
            $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
            $(this).parent(".result").empty();
        });
    });
  </script>
  </head>
  <body>
    <h1>Welcome to the Caregiver Community!</h1>

    <div class="button-container">
      <button><a href="profile.php">View Your Profile</a></button>
      <button><a href="create-a-contract.php">Create a Contract</a></button>
      <button><a href="contract.php">Contracts</a></button>
      <button><a href="pending.php">Pending Contracts/Pending Reviews</a>
      </button>
    </div>

    <div class="search-box">
        <input type="text" autocomplete="off" placeholder="Search for a Caregiver..." />
        <table class="result"></table>
    </div>
  </body>
</html>
