<?php
require_once '../../config/connect.php';

// Start the session to access session data
session_start();

// Check if the task_ids parameter is present in the POST request as an array
if (isset($_POST['task_id']) && is_array($_POST['task_id'])) {
    $task_ids = $_POST['task_id'];
    $projectowner_id = $_POST['projectowner_id'];

    $loggedInUserId = $_SESSION['user_id'];

    // Initialize an array to store the results of each deletion
    $deletionResults = array();

    // Loop through each task ID in the array
    foreach ($task_ids as $task_id) {
        if (is_numeric($task_id)) {
            // Get the task's creator ID from the database
            $taskCreatorIdSql = "SELECT task_creator_id FROM Tasks WHERE task_id='$task_id'";
            $taskCreatorResult = $connection->query($taskCreatorIdSql);

            if ($taskCreatorResult->num_rows > 0) {
                $taskCreatorData = $taskCreatorResult->fetch_assoc();
                $taskCreatorId = $taskCreatorData['task_creator_id'];
            }

            // Check if the user is the project owner or the task creator
            if ($projectowner_id == $loggedInUserId || $taskCreatorId == $loggedInUserId) {

                // Delete messages related to the task
                $delete_messages_query = "DELETE FROM Messages WHERE task_id = ?";
                $delete_messages_stmt = $connection->prepare($delete_messages_query);
                $delete_messages_stmt->bind_param("i", $task_id);
                $delete_messages_stmt->execute();
                $delete_messages_stmt->close();

                // Delete the task
                $delete_task_query = "DELETE FROM Tasks WHERE task_id = ?";
                $delete_task_stmt = $connection->prepare($delete_task_query);
                $delete_task_stmt->bind_param("i", $task_id);

                if ($delete_task_stmt->execute()) {
                    // Task deleted successfully
                    $deletionResults[] = array('task_id' => $task_id, 'status' => 'success', 'message' => 'Task and related messages deleted successfully.');
                } else {
                    // Error deleting task
                    $deletionResults[] = array('task_id' => $task_id, 'status' => 'error', 'message' => 'Error deleting task and messages.');
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

    // Return the results of the deletions as JSON
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'success', 'message' => 'Task and related messages deleted successfully.'));
} else {
    // No task IDs provided or task_ids parameter is not an array
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Invalid task IDs.'));
}
?>