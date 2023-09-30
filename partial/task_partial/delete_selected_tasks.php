<?php
require_once '../../config/connect.php';
include '../utils.php';
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access session data
session_start();

// Check if the task_ids parameter is present in the POST request as an array
if (isset($_POST['task_id']) && is_array($_POST['task_id'])) {
    $task_ids = $_POST['task_id'];
    $projectowner_id = $_POST['projectowner_id'];

    $loggedInUserId = $_SESSION['user_id'];

    // Initialize an array to store the results of each deletion
    $deletionResults = array();

    // Prepare a statement to insert recent activity
    $insert_recent_activity_query = "INSERT INTO RecentActivity (activity_type, activity_description, project_id) VALUES (?, ?, ?)";
    $insert_recent_activity_stmt = $connection->prepare($insert_recent_activity_query);

    if (!$insert_recent_activity_stmt) {
        die("Error preparing recent activity statement: " . $connection->error);
    }

    // Loop through each task ID in the array
    foreach ($task_ids as $task_id) {
        if (is_numeric($task_id)) {
            // Get the task's creator ID from the database
            $taskCreatorIdSql = "SELECT task_creator_id FROM Tasks WHERE task_id=?";
            $taskCreatorStmt = $connection->prepare($taskCreatorIdSql);

            if (!$taskCreatorStmt) {
                die("Error preparing task creator statement: " . $connection->error);
            }

            $taskCreatorStmt->bind_param("i", $task_id);
            $taskCreatorStmt->execute();
            $taskCreatorResult = $taskCreatorStmt->get_result();

            if ($taskCreatorResult->num_rows > 0) {
                $taskCreatorData = $taskCreatorResult->fetch_assoc();
                $taskCreatorId = $taskCreatorData['task_creator_id'];
            }

            $taskCreatorStmt->close();

            // Check if the user is the project owner or the task creator
            if ($projectowner_id == $loggedInUserId || $taskCreatorId == $loggedInUserId) {

                // Delete messages related to the task
                $delete_messages_query = "DELETE FROM Messages WHERE task_id=?";
                $delete_messages_stmt = $connection->prepare($delete_messages_query);

                if (!$delete_messages_stmt) {
                    die("Error preparing delete messages statement: " . $connection->error);
                }

                $delete_messages_stmt->bind_param("i", $task_id);
                $delete_messages_stmt->execute();
                $delete_messages_stmt->close();

                // Delete the task
                $delete_task_query = "DELETE FROM Tasks WHERE task_id=?";
                $delete_task_stmt = $connection->prepare($delete_task_query);

                if (!$delete_task_stmt) {
                    die("Error preparing delete task statement: " . $connection->error);
                }

                $delete_task_stmt->bind_param("i", $task_id);

                if ($delete_task_stmt->execute()) {
                    // Task deleted successfully

                    // Insert recent activity
                    $activity_description = "Task with ID '$task_id' deleted by user '" . getUserName($loggedInUserId) . "'";
                    $activity_type = "Task Deleted";

                    $insert_recent_activity_stmt->bind_param("ssi", $activity_type, $activity_description, $project_id);
                    if ($insert_recent_activity_stmt->execute()) {
                        $deletionResults[] = array('task_id' => $task_id, 'status' => 'success', 'message' => 'Task and related messages deleted successfully.');
                    } else {
                        $deletionResults[] = array('task_id' => $task_id, 'status' => 'error', 'message' => 'Error inserting recent activity: ' . $connection->error);
                    }
                } else {
                    // Error deleting task
                    $deletionResults[] = array('task_id' => $task_id, 'status' => 'error', 'message' => 'Error deleting task and messages: ' . $connection->error);
                }

                $delete_task_stmt->close();
            } else {
                // User is not authorized to delete this task
                $deletionResults[] = array('task_id' => $task_id, 'status' => 'error', 'message' => 'You are neither the project owner nor the designated assignee for this task.');
            }
        } else {
            // Invalid task ID in the array
            $deletionResults[] = array('task_id' => $task_id, 'status' => 'error', 'message' => 'Invalid task ID.');
        }
    }

    // Close the insert recent activity statement
    $insert_recent_activity_stmt->close();

    // Return the results of the deletions as JSON
    header('Content-Type: application/json');
    echo json_encode($deletionResults);
} else {
    // No task IDs provided or task_ids parameter is not an array
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Invalid task IDs.'));
}
?>
