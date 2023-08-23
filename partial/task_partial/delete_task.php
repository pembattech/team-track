<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require_once '../../config/connect.php';

// Check if the task_id parameter is present in the POST request
if (isset($_POST['task_id']) && is_numeric($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $projectowner_id = $_POST['projectowner_id'];

    $loggedInUserId = $_SESSION['user_id'];

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
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'success', 'message' => 'Task and related messages deleted successfully.'));
        } else {
            // Error deleting task
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Error deleting task and messages.'));
        }

        $delete_task_stmt->close();
        $connection->close();
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