<?php
require_once '../../config/connect.php';

// Start the session to access session data
session_start();

// Check if the task_ids parameter is present in the POST request as an array
if (isset($_POST['task_id']) && is_array($_POST['task_id'])) {
    $task_ids = $_POST['task_id'];
    $projectowner_id = $_POST['projectowner_id'];

    $loggedInUserId = $_SESSION['user_id'];

    echo $task_id;

    // $deletedTasks = [];

    // // Loop through each task ID and delete the tasks
    // foreach ($task_ids as $task_id) {
    //     // Get the task's creator ID from the database
    //     $taskCreatorIdSql = "SELECT task_creator_id FROM Tasks WHERE task_id=?";
    //     $taskCreatorStmt = $connection->prepare($taskCreatorIdSql);
    //     $taskCreatorStmt->bind_param("i", $task_id);
    //     $taskCreatorStmt->execute();
    //     $taskCreatorResult = $taskCreatorStmt->get_result();

    //     if ($taskCreatorResult->num_rows > 0) {
    //         $taskCreatorData = $taskCreatorResult->fetch_assoc();
    //         $taskCreatorId = $taskCreatorData['task_creator_id'];

    //         // Check if the user is the project owner or the task creator
    //         if ($projectowner_id == $loggedInUserId || $taskCreatorId == $loggedInUserId) {
    //             // Delete messages related to the task
    //             $delete_messages_query = "DELETE FROM Messages WHERE task_id = ?";
    //             $delete_messages_stmt = $connection->prepare($delete_messages_query);
    //             $delete_messages_stmt->bind_param("i", $task_id);
    //             $delete_messages_stmt->execute();
    //             $delete_messages_stmt->close();

    //             // Delete the task
    //             $delete_task_query = "DELETE FROM Tasks WHERE task_id = ?";
    //             $delete_task_stmt = $connection->prepare($delete_task_query);
    //             $delete_task_stmt->bind_param("i", $task_id);

    //             if ($delete_task_stmt->execute()) {
    //                 // Task deleted successfully
    //                 $deletedTasks[] = $task_id;
    //             }
    //             $delete_task_stmt->close();
    //         }
    //     }
    // }

    // // Check if any tasks were deleted
    // if (!empty($deletedTasks)) {
    //     header('Content-Type: application/json');
    //     echo json_encode(array('status' => 'success', 'message' => 'Tasks and related messages deleted successfully.', 'deletedTasks' => $deletedTasks));
    // } else {
    //     // No tasks were deleted, or the user doesn't have permission
    //     header('Content-Type: application/json');
    //     echo json_encode(array('status' => 'error', 'message' => 'No tasks were deleted or you are neither the project owner nor the designated assignee for some tasks.'));
    // }

    $connection->close();
} else {
    // Invalid task IDs or no task IDs provided
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Invalid task IDs.'));
}
?>