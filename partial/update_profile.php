<?php
require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (isset($_POST["submit"])) {
    $user_id = $_SESSION['user_id'];
    $about = $_POST["about"];
    $profile_picture = $_FILES["new-profilepic"];

    // Check if a new profile picture is uploaded
    if (isset($profile_picture) && $profile_picture['error'] === 0 && $profile_picture['size'] > 0) {
        echo "pic";
        $targetDir = "profile_pictures/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($profile_picture["name"]);


        $targetFilePath = $targetDir . $fileName;


        echo $targetFilePath;

        if (move_uploaded_file($profile_picture["tmp_name"], $targetFilePath)) {
            $profile_picture_path = "partial/" . $targetFilePath;
            $update_profile_query = "UPDATE Users SET about = '$about', profile_picture = '$profile_picture_path' WHERE user_id = $user_id";

            if ($connection->query($update_profile_query) === TRUE) {
                echo "Profile information updated successfully!";
                $_SESSION['notification_message'] = "Profile information updated successfully.";
            } else {
                echo "Error updating profile information: " . $connection->error;
                $_SESSION['notification_message'] = "Error updating profile information.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "about";
        $update_profile_query = "UPDATE Users SET about = '$about' WHERE user_id = $user_id";

        if ($connection->query($update_profile_query) === TRUE) {
            echo "Profile information updated successfully!";
            $_SESSION['notification_message'] = "Profile information updated successfully.";
        } else {
            echo "Error updating profile information: " . $connection->error;
            $_SESSION['notification_message'] = "Error updating profile information.";
        }
    }

    // Redirect to profile page after processing the form
    header("Location: ../profile.php");
}
?>
