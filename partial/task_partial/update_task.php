<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/connect.php';

// Start the session to access session data
session_start();

function getUserName($user_id) {
    global $connection;

    $sql = "SELECT name FROM Users WHERE user_id = $user_id";
    $result = $connection->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Team member"; // Default if user not found
    }
}


// Check if the form data has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the task ID and project ID from the form data
    $task_id = $_POST["task_id"];
    $project_id = $_POST["project_id"];
    $projectowner_id = $_POST['projectowner_id'];
    $task_name = $_POST["task_name"];
    $task_description = $_POST["task_description"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $status = $_POST["status"];
    $priority = $_POST["priority"];
    $assignee = $_POST["assignee"];

    $loggedInUserId = $_SESSION['user_id'];

    if ($projectowner_id != $loggedInUserId) {

        // Construct a message to be sent to the project owner
        $message = "The task with ID $task_id has been updated by " . getUserName($loggedInUserId);

        // Insert the message into the Messages table
        $insert_message_query = "INSERT INTO Messages (task_id, recipient_id, text, is_task_msg) VALUES ($task_id, $projectowner_id, '$message', 1)";
        

        if (mysqli_query($connection, $insert_message_query)) {
            echo " ";
        } else {
            echo " ";
        }
    }

    // Construct the SQL query
    $sql = "UPDATE Tasks SET task_name = '$task_name', task_description = '$task_description', assignee = '$assignee', start_date = '$start_date', end_date = '$end_date', status = '$status', priority = '$priority' WHERE task_id = $task_id";

    // Execute the query and check for success
    if ($connection->query($sql)) {
        // Check if any rows were affected
        if ($connection->affected_rows > 0) {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'success', 'message' => 'Task update successfully.'));

        } else {
            // Task update failed
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'An error occurred while updating the task.'));
            
        }
    } else {
        // Error executing the query
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'An error occurred while updating the taskk..'));
    }
}

$connection->close();
?>
