<?php
// Database credentials
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'teamtrack';

// Create a database connection
$connection = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
} else {
    // Start a session to access session variables (if needed)
    session_start();

    // Check if the user ID is set in the session
    if (isset($_SESSION['user_id'])) {
        // Get the user ID of the logged-in user
        $user_id = $_SESSION['user_id'];   
        echo "";
        // echo "Successfully connected to $database. User ID: $user_id";
    } else {
        echo "";
        // echo "Successfully connected to $database.";
    }
}
?>
