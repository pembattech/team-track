<?php
require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (isset($_POST["about"])) {
    $user_id = $_SESSION['user_id'];
    $about = $_POST["about"];

    $update_profile_query = "UPDATE Users SET about = '$about' WHERE user_id = $user_id";

    if ($connection->query($update_profile_query) === TRUE) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'success', 'message' => 'About section update successfully.'));

    } else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'An error occur while updating about.'));
    }
}


?>