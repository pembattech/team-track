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

    // Prepare and execute the SQL query using a prepared statement
    $stmt = $connection->prepare("UPDATE Tasks SET task_name = ?, task_description = ?, start_date = ?, end_date = ?, status = ?, priority = ? WHERE task_id = ?");
    $stmt->bind_param("ssssssi", $task_name, $task_description, $start_date, $end_date, $status, $priority, $task_id);
    $stmt->execute();

    // Check if the task update was successful
    if ($stmt->affected_rows > 0) {
        // Task updated successfully
        echo "Task updated successfully.";
    } else {
        // Task update failed
        echo "Error updating task. Please try again.";
    }

    $stmt->close();

}

$connection->close();
?>

