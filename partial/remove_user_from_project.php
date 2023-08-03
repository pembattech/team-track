<?php
require_once '../config/connect.php';

if (isset($_POST['remove_user'])) {
    $project_id = $_POST['project_id'];
    $user_id = $_POST['user_id'];

    echo $project_id, $user_id;

    // Query to remove the user from the ProjectUsers table
    $sql_remove_user = "DELETE FROM ProjectUsers WHERE project_id = $project_id AND user_id = $user_id";

    // Execute the query
    if (mysqli_query($connection, $sql_remove_user)) {
        echo "User removed from the project successfully.";
        $_SESSION['notification_message'] = "User removed from the project successfully.";
    } else {
        echo "Error removing user from the project: " . mysqli_error($connection);
        $_SESSION['notification_message'] = "Error removing user from the project.";
    }

    header("Location: ../project.php?project_id=$project_id");

}
?>