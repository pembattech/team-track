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

        // Return the task details as a JSON response
        header('Content-Type: application/json');
        echo json_encode($task);
    } else {
        // Return an error response if the task was not found
        header("HTTP/1.0 404 Not Found");
        echo json_encode(array('error' => 'Task not found.'));
    }

    $stmt->close();
} else {
    // Return an error response if 'task_id' is missing or not numeric
    header("HTTP/1.0 400 Bad Request");
    echo json_encode(array('error' => 'Invalid request.'));
}

$connection->close();
?>