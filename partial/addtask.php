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
    $response = array();
    $project_id = sanitize_input($_POST['project_id']);
    $taskname = sanitize_input($_POST['taskname']);
    $status = "New";
    $user_id = $_SESSION['user_id'];
    $section = "To Do";

    // Check if the 'taskname' field is present and not empty
    if (empty($_POST['taskname'])) {
        $response['status'] = 'error';
        $response['message'] = 'Task Name is required ';
    } else {
        $response['status'] = 'success';
        $response['message'] = 'Task added successfully.';
        $insert_project_query = "INSERT INTO Tasks (project_id, user_id, task_name, status, section) VALUES ('$project_id', '$user_id', '$taskname', '$status', '$section')";

        if ($connection->query($insert_project_query) === TRUE) {
            echo "New task added successfully!";
            $response['status'] = 'success';
            $response['message'] = 'Task added successfully.';
            $_SESSION['notification_message'] = "New task added successfully!";

            // Send the response back to the client as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            echo "Error adding new task: " . $connection->error;
            $response['status'] = 'error';
            $_SESSION['notification_message'] = "Error adding new task.";
        }

        header("Location: ../project.php?project_id=$project_id");

    }

}
?>