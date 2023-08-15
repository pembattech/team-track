<?php
session_start(); // Start the session

require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$newRole = $_POST['new_role'];

echo $newRole;

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $userId = $_POST['user_id'];
//     $projectId = $_POST['project_id'];
//     $newRole = $_POST['new_role'];

//     echo $newRole;

    // $sql_update_userrole = "UPDATE ProjectUsers SET user_role = '$newRole' WHERE project_id = $projectId AND user_id = $userId";

    // $result = mysqli_query($connection, $sql_update_userrole);

    // if ($result) {
    //     echo 'Success';
    //     $_SESSION['notification_message'] = "User role updated successfully.";
    // } else {
    //     echo 'Error';
    //     $_SESSION['notification_message'] = "Error updating user role.";
    // }
// }
?>
