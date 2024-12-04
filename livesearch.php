<?php
require 'dp.php';
session_start();
$link = getDatabaseMysqli();

if(isset($_REQUEST["term"])){
    // Prepare a select statement
    $sql = "SELECT * FROM MEMBER WHERE Username LIKE ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_term);
        
        // Set parameters
        $param_term = $_REQUEST["term"] . '%';
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            // Check number of rows in the result set
            if(mysqli_num_rows($result) > 0){
                echo    "<tr>
                            <th>Username:</th>
                            <th>Address:</th>
                            <th>Phone Number:</th>
                            <th>Rating:</th>
                        </tr>";
                // Fetch result rows as an associative array
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    echo    "<tr>",
                                "<td>", $row["Username"], "</td>",
                                "<td>", $row["Address"], "</td>",
                                "<td>", $row["PhoneNumber"], "</td>",
                                "<td>", $row["Rating"], "</td>",
                            "</tr>";
                }
            } else{
                echo "<p>No matches found</p>";
            }
        } else{
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
        }
    }
        
    // Close statement
    mysqli_stmt_close($stmt);
}
?>
