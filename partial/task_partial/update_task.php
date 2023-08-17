<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/connect.php';


// Check if the form data has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the task ID and project ID from the form data
    $task_id = $_POST["task_id"];
    $project_id = $_POST["project_id"];

    // Get the edited task details from the form data
    $task_name = $_POST["task_name"];
    $task_description = $_POST["task_description"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $status = $_POST["status"];
    $priority = $_POST["priority"];
    $assignee = $_POST["member_id"];

    // Construct the SQL query
    $sql = "UPDATE Tasks SET task_name = '$task_name', task_description = '$task_description', assignee = '$assignee', start_date = '$start_date', end_date = '$end_date', status = '$status', priority = '$priority' WHERE task_id = $task_id";

    // Execute the query and check for success
    if ($connection->query($sql)) {
        // Check if any rows were affected
        if ($connection->affected_rows > 0) {
            // Task updated successfully
            echo "Task updated successfully.";
        } else {
            // Task update failed
            echo "Error updating task. Please try again.";
        }
    } else {
        // Error executing the query
        echo "An error occurred while updating the task: " . $connection->error;
    }

}

$connection->close();
?>