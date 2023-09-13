<?php
require_once '../../config/connect.php';

// Check if the 'task_id' parameter is present in the GET request
if (isset($_GET['task_id']) && is_numeric($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    $stmt = $connection->prepare("SELECT Tasks.*, Users.user_id FROM Tasks
                                  LEFT JOIN Users ON Tasks.assignee = Users.user_id
                                  WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the task was found
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
        echo json_encode($task);
    } else {
        // Return an error response if the task was not found
        http_response_code(404);
        echo json_encode(array('error' => 'Task not found.'));
    }

    $stmt->close();
} else {
    // Return an error response if the task was not found
    http_response_code(404);
    echo json_encode(array('error' => 'Invalid request.'));
}

$connection->close();
?>