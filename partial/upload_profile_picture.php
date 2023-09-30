<?php
require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $profile_picture = $_FILES["new-profilepic"];

    // Check if a new profile picture is uploaded
    if (isset($profile_picture) && $profile_picture['error'] === 0 && $profile_picture['size'] > 0) {
        $targetDir = "profile_pictures/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($profile_picture["name"]);


        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($profile_picture["tmp_name"], $targetFilePath)) {
            $profile_picture_path = "partial/" . $targetFilePath;
            $update_profile_query = "UPDATE Users SET profile_picture = '$profile_picture_path' WHERE user_id = $user_id";

            if ($connection->query($update_profile_query) === TRUE) {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => 'Profile picture update successully.', 'newProfilePicture' => $profile_picture_path));
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'An error occur while updating profile picture.'));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'An error occur while updating profile picture.'));
        }
    }

}
?>