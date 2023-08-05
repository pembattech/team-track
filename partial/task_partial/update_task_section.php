<?php
require_once '../../config/connect.php';


// Get the task ID and the destination section from the AJAX request
if (isset($_POST['task_id']) && isset($_POST['section'])) {
    $taskId = $_POST['task_id'];
    $section = $_POST['section'];

    // Update the task's section in the database
    $sql = "UPDATE Tasks SET section='$section' WHERE task_id='$taskId'";

    if ($connection->query($sql) === TRUE) {
        // Return a success response if the update is successful
        echo json_encode(array('status' => 'success', 'message' => 'Task section updated successfully.'));
    } else {
        // Return an error response if there is an issue with the update
        echo json_encode(array('status' => 'error', 'message' => 'Error updating task section: ' . $connection->error));
    }
} else {
    // Return an error response if the required parameters are not provided
    echo json_encode(array('status' => 'error', 'message' => 'Invalid parameters.'));
}

$connection->close();
?>
