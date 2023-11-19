<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../../config/connect.php';
require_once '../utils.php'; // Include the utils.php file

// Start the session to access session data
session_start();

// Check if the task_id parameter is present in the POST request
if (isset($_POST['task_id']) && is_numeric($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $projectowner_id = $_POST['projectowner_id'];
    $project_id = $_POST['project_id'];

    $task_name = getTaskInfo($task_id)['task_name'];


    $task_details = getTaskDetails($task_id);

    $loggedInUserId = $_SESSION['user_id'];

    // Get the task's creator ID from the database
    $taskCreatorIdSql = "SELECT task_creator_id FROM Tasks WHERE task_id=?";
    $taskCreatorStmt = $connection->prepare($taskCreatorIdSql);

    if ($taskCreatorStmt) {
        $taskCreatorStmt->bind_param("i", $task_id);
        $taskCreatorStmt->execute();
        $taskCreatorResult = $taskCreatorStmt->get_result();

        if ($taskCreatorResult->num_rows > 0) {
            $taskCreatorData = $taskCreatorResult->fetch_assoc();
            $taskCreatorId = $taskCreatorData['task_creator_id'];
        }

        // $taskCreatorStmt->close();
    }

    // Check if the user is the project owner or the task creator
    if ($projectowner_id == $loggedInUserId || $taskCreatorId == $loggedInUserId) {

        // Delete messages related to the task
        $delete_messages_query = "DELETE FROM Messages WHERE task_id = ?";
        $delete_messages_stmt = $connection->prepare($delete_messages_query);

        if ($delete_messages_stmt) {
            $delete_messages_stmt->bind_param("i", $task_id);
            $delete_messages_stmt->execute();
            // $delete_messages_stmt->close();

            // Delete the task
            $delete_task_query = "DELETE FROM Tasks WHERE task_id = ?";
            $delete_task_stmt = $connection->prepare($delete_task_query);

            if ($delete_task_stmt) {
                $delete_task_stmt->bind_param("i", $task_id);

                if ($delete_task_stmt->execute()) {
                    // Task deleted successfully

                    if ($projectowner_id != $taskCreatorId) {

                        $messageText = 'The task ' . $task_name . ' you created has been deleted by the project owner.';
                        $insert_message_query = "INSERT INTO Messages (recipient_id, text, project_id, is_task_msg) VALUES ($taskCreatorId, '$messageText', $project_id, 1)";

                        if ($connection->query($insert_message_query)) {
                            echo '';
                        } else {
                            echo '';
                        }
                    }

                    // Get the user's name using the getUserName function from utils.py
                    $userName = getUserName($loggedInUserId);

                    // Insert recent activity
                    $activity_description = "'$task_name' task deleted by user '$userName'";
                    $activity_type = "Task Deleted";

                    $insert_recent_activity_query = "INSERT INTO RecentActivity (activity_type, activity_description, project_id) VALUES (?, ?, ?)";
                    $insert_recent_activity_stmt = $connection->prepare($insert_recent_activity_query);

                    if ($insert_recent_activity_stmt) {
                        $insert_recent_activity_stmt->bind_param("ssi", $activity_type, $activity_description, $project_id);

                        if ($insert_recent_activity_stmt->execute()) {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'success', 'message' => 'Task and related messages deleted successfully.'));
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'error', 'message' => 'Error inserting recent activity: ' . $connection->error));
                        }

                        $insert_recent_activity_stmt->close();
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => 'Error preparing recent activity statement: ' . $connection->error));
                    }
                } else {
                    // Error deleting task
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'Error deleting task and messages: ' . $connection->error));
                }

                $delete_task_stmt->close();
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error preparing delete task statement: ' . $connection->error));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Error preparing delete messages statement: ' . $connection->error));
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'You are neither the project owner nor the designated assignee for this task.'));
    }
} else {
    // Invalid task ID or no task ID provided
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Invalid task ID.'));
}
?>