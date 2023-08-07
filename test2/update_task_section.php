<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the task ID and the destination section from the AJAX request
if (isset($_POST['task_id']) && isset($_POST['section'])) {
    $taskId = $_POST['task_id'];
    $section = $_POST['section'];

    // Update the task's section in the database
    $sql = "UPDATE Tasks SET section='$section' WHERE task_id='$taskId'";

    if ($conn->query($sql) === TRUE) {
        // Return a success response if the update is successful
        echo json_encode(array('status' => 'success', 'message' => 'Task section updated successfully.'));
    } else {
        // Return an error response if there is an issue with the update
        echo json_encode(array('status' => 'error', 'message' => 'Error updating task section: ' . $conn->error));
    }
} else {
    // Return an error response if the required parameters are not provided
    echo json_encode(array('status' => 'error', 'message' => 'Invalid parameters.'));
}

$conn->close();
?>
