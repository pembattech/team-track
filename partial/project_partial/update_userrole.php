<?php
require_once '../../config/connect.php';
include '../utils.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $projectId = $_POST['project_id'];
    $newRole = $_POST['new_role'];

    $projectName = get_project_data($projectId)["project_name"];

    function isValidRole($role)
    {
        // Check if the role is not a number and doesn't start with a number or special character
        return !is_numeric($role) && preg_match('/^[a-zA-Z]/', $role);
    }

    if (!isValidRole($newRole)) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'Invalid user role format.'));
        return;
    } else {
        $sql_fetch_userrole = "SELECT user_role FROM ProjectUsers WHERE project_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($connection, $sql_fetch_userrole);

        if ($stmt) {
            // Bind parameters to the statement
            mysqli_stmt_bind_param($stmt, "ii", $projectId, $userId);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Get the result
            mysqli_stmt_bind_result($stmt, $old_userRole);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            $sql_update_userrole = "UPDATE ProjectUsers SET user_role = ? WHERE project_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($connection, $sql_update_userrole);

            if ($stmt) {
                // Bind parameters to the statement
                mysqli_stmt_bind_param($stmt, "sii", $newRole, $projectId, $userId);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    // Construct the message text with htmlspecialchars to handle quotes and special characters
                    $message_text = 'There has been an update to your role within the "' . htmlspecialchars($projectName, ENT_QUOTES) . '" project.';

                    if (!empty($old_userRole)) {
                        $message_text .= ' Previously, you held the role of "' . htmlspecialchars($old_userRole, ENT_QUOTES) . '",';
                    }

                    $message_text .= ' and now you have been assigned the role of "' . htmlspecialchars($newRole, ENT_QUOTES) . '".';

                    $is_project_msg_value = '1';

                    $insert_message_query = "INSERT INTO Messages (recipient_id, text, project_id, is_project_msg) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($connection, $insert_message_query);

                    if ($stmt) {
                        // Bind parameters to the statement
                        mysqli_stmt_bind_param($stmt, "issi", $userId, $message_text, $projectId, $is_project_msg_value);

                        // Execute the statement
                        if (mysqli_stmt_execute($stmt)) {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'success', 'message' => 'User role updated successfully.'));
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'error', 'message' => 'Error inserting message.'));
                        }
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => 'Error preparing message insertion query.'));
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'Error updating user role.'));
                }

                mysqli_stmt_close($stmt);
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error preparing user role update query.'));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Error preparing user role fetch query.'));
        }
    }
}
?>