<?php
require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $projectId = $_POST['project_id'];
    $newRole = $_POST['new_role'];

    function isValidRole($role)
    {
        // Check if the role is not a number and doesn't start with a number or special character
        return !is_numeric($role) && preg_match('/^[a-zA-Z]/', $role);
    }

    if (!isValidRole($newRole)) {
        echo 'Error: Invalid user role format.';
        $_SESSION['notification_message'] = "Invalid user role format.";
        return;
    } else {


        $sql_fetch_userrole = "SELECT user_role FROM ProjectUsers WHERE project_id = $projectId AND user_id = $userId";

        // Query the project name
        $sql_fetch_projectname = "SELECT project_name FROM Projects WHERE project_id = $projectId";
        $projectNameResult = mysqli_query($connection, $sql_fetch_projectname);
        $projectNameRow = mysqli_fetch_assoc($projectNameResult);
        $projectName = $projectNameRow['project_name'];

        $result_old_userrole = mysqli_query($connection, $sql_fetch_userrole);

        if ($result_old_userrole) {
            $row = mysqli_fetch_assoc($result_old_userrole);
            $old_userRole = $row['user_role'];

            $sql_update_userrole = "UPDATE ProjectUsers SET user_role = '$newRole' WHERE project_id = $projectId AND user_id = $userId";
            $result = mysqli_query($connection, $sql_update_userrole);

            if ($result) {
                echo 'Success';
                $_SESSION['notification_message'] = "User role updated successfully.";

                // Construct the message text
                $message_text = 'There has been an update to your role within the "' . $projectName . '" project.';

                if (!empty($old_userRole)) {
                    $message_text .= ' Previously, you held the role of "' . $old_userRole . '",';
                }

                $message_text .= ' and now you have been assigned the role of "' . $newRole . '".';

                $insert_message_query = "INSERT INTO Messages (recipient_id, text) VALUES ('$userId', '$message_text')";

                // Execute the query to insert the message into the database
                if (mysqli_query($connection, $insert_message_query)) {
                    echo '';
                }
            } else {
                echo 'Error'; // Return 'Error' if update fails
                $_SESSION['notification_message'] = "Error updating user role.";
            }
        }
    }
}
?>