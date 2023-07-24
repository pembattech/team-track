<?php
// logout.php

// Start the session (if it has not been started already)
session_start();

// Function to log out the user and destroy the session
function logout()
{
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect the user to the login page or any other desired page
    header("Location: login.php"); // Change 'login.php' to your login page URL
    exit; // Ensure that no further code is executed after the redirect
}

// Call the logout function when the user clicks the logout button or performs the logout action
logout();
?>
