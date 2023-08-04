<?php

require_once '../config/connect.php';

// Handle the AJAX request to update the task status and section in the database
if (isset($_POST['task_id']) && isset($_POST['new_section'])) {
    $task_id = $_POST['task_id'];
    $new_section = $_POST['new_section'];

    // Update the task's section in the Tasks table
    $sql = "UPDATE Tasks SET section = '$new_section' WHERE task_id = $task_id";

    if (mysqli_query($connection, $sql)) {
        $response = array('status' => 'success');
    } else {
        $response = array('status' => 'error', 'message' => mysqli_error($connection));
    }

    // Send the response back to the client as JSON
    echo json_encode($response);
}

// Close the database connection
mysqli_close($connection);
?>
