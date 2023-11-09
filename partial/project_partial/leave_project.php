<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require_once '../../config/connect.php';
include '../utils.php';

session_start();

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
    $user_id = $_SESSION['user_id'];

    // Query to remove the user from the ProjectUsers table
    $sql_remove_user = "DELETE FROM ProjectUsers WHERE project_id = $project_id AND user_id = $user_id";

    // Execute the query
    if (mysqli_query($connection, $sql_remove_user)) {
        $activity_type = "User Left";
        $activity_description = "User '" . getUserName($user_id) . "' has left the project.";
        $sql = "INSERT INTO RecentActivity (user_id, activity_type, activity_description, project_id) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("isss", $user_id, $activity_type, $activity_description, $project_id);

            if ($stmt->execute()) {
                echo '';
            } else {
                echo '';
            }
        } else {
            echo '';
        }


        echo 'Success';
        $_SESSION['notification_message'] = "You have successfully left the project.";
    } else {
        echo "Error removing user from the project: " . mysqli_error($connection);
        $_SESSION['notification_message'] = "An error occurred while trying to leave the project.";
    }

    header("Location: ../../home.php");
}
?>