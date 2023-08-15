<?php

require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the task_id from the AJAX request
if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    // Sanitize the input if necessary

    // Update the task status in the database to "Completed"
    $sql = "UPDATE Tasks SET status = 'Completed' WHERE task_id = $task_id";
    $result = mysqli_query($connection, $sql);

    if ($result) {
        // Update successful, send a response back to the JavaScript function
        echo "success";
    } else {
        // Update failed, send an error response back to the JavaScript function
        echo "error";
    }
}
?>

