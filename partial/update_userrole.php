<?php
require_once '../config/connect.php';

if (isset($_POST['update_userrole'])) {
    $project_id = $_POST['project_id'];
    $user_id = $_POST['user_id'];
    $user_role = $_POST['user-role'];

    $sql_update_userrole = "UPDATE ProjectUsers SET user_role = '$user_role' WHERE project_id = $project_id AND user_id = $user_id";

    // Execute the query
    if (mysqli_query($connection, $sql_update_userrole)) {
        echo "User role updated successfully.";
        $_SESSION['notification_message'] = "User role updated successfully.";
    } else {
        echo "Error updating user role: " . mysqli_error($connection);
        $_SESSION['notification_message'] = "Error updating user role.";
    }
    header("Location: ../project.php?project_id=$project_id");
}
?>
