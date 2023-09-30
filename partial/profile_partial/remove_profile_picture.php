<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<?php

require_once '../../config/connect.php';

// Start the session to access session data
session_start();

// Get the user ID from the AJAX request
$user_id = $_SESSION['user_id'];

// Update the profile picture path to an empty value
$update_query = "UPDATE Users SET profile_picture = '' WHERE user_id = $user_id";

$response = array();

if ($connection->query($update_query) === TRUE) {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'success', 'message' => 'Remove profile picture successfully.'));
} else {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Error removing profile picture.'));
}

?>