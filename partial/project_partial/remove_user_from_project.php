<?php
require_once '../../config/connect.php';

include '../utils.php';

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
        $activity_type = "Remove User";
        $activity_description = "User '" . getUserName($userId) . "' has been removed from the project.";
        $sql = "INSERT INTO RecentActivity (user_id, activity_type, activity_description, project_id) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("isss", $userId, $activity_type, $activity_description, $projectId);

            if ($stmt->execute()) {
                echo '';
            } else {
                echo '';
            }
        } else {
            echo '';
        }

        echo 'Success';
        $_SESSION['notification_message'] = "User removed from the project successfully.";



    } else {
        echo "Error removing user from the project: " . mysqli_error($connection);
        $_SESSION['notification_message'] = "Error removing user from the project.";
    }
}
?>