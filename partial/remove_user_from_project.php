<?php
require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectId = $_POST['project_id'];
    $userId = $_POST['user_id'];

    // Query to remove the user from the ProjectUsers table
    $sql_remove_user = "DELETE FROM ProjectUsers WHERE project_id = $projectId AND user_id = $userId";
    
    // Execute the query
    if (mysqli_query($connection, $sql_remove_user)) {
        echo 'Success';
        $_SESSION['notification_message'] = "User removed from the project successfully.";
    } else {
        echo "Error removing user from the project: " . mysqli_error($connection);
        $_SESSION['notification_message'] = "Error removing user from the project.";
    }

    // header("Location: ../project.php?project_id=$project_id");

}
?>