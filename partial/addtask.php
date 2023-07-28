<?php
session_start();

require_once '../config/connect.php';


// Function to sanitize user inputs
function sanitize_input($input)
{
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = sanitize_input($_POST['project_id']);
    $taskname = sanitize_input($_POST['taskname']);
    $assignee = sanitize_input($_POST['assignee']);
    // $end_date = sanitize_input($_POST['duedate']);
    $priority = sanitize_input($_POST['priority']);
    $status = sanitize_input($_POST['status']);
    $user_id = $_SESSION['user_id'];
    $section = "To Do";

    echo $taskname, $assignee, $duedate, $priority, $status, $user_id;
    echo $project_id;

    $insert_project_query = "INSERT INTO Tasks (project_id, user_id, task_name, status, section, priority) VALUES ('$project_id', '$user_id', '$taskname', '$status', '$section', '$priority')";

    echo $insert_project_query;

    if ($connection->query($insert_project_query) === TRUE) {
        echo "New task added successfully!";
        $_SESSION['notification_message'] = "New task added successfully!";
    } else {
        echo "Error adding new task: " . $connection->error;
        $_SESSION['notification_message'] = "Error adding new task.";
    }

    header("Location: ../project.php?project_id=$project_id");

}
?>