<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


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
    $task_description = sanitize_input($_POST['task_description']);
    $status = "New";
    $user_id = $_SESSION['user_id'];
    $section = "To Do";
    $is_newtask_msg = 1;


    // Check if the 'taskname' field is present and not empty
    if (empty($_POST['taskname'])) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'Task name is required'));
        exit;
        
    } elseif (empty($_POST['task_description'])) {
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'Task description is required'));
        exit;

    } else {
        $project_owner_query = "SELECT * FROM ProjectUsers WHERE project_id = '$project_id' AND is_projectowner='1'";
        $projectuser_result = mysqli_query($connection, $project_owner_query);

        if ($projectuser_result && mysqli_num_rows($projectuser_result) > 0) {
            $projectuser_row = mysqli_fetch_assoc($projectuser_result);
            $projectuser_id = $projectuser_row['projectuser_id'];
            $project_owner_id = $projectuser_row['user_id'];

            // Insert the task into the Tasks table
            $insert_project_query = "INSERT INTO Tasks (projectuser_id, task_creator_id, task_name, task_description, status, section) VALUES ('$projectuser_id', '$user_id', '$taskname', '$task_description', '$status', '$section')";

            if ($connection->query($insert_project_query) === TRUE) {
                $task_id = mysqli_insert_id($connection); // Get the last inserted task_id

                if ($project_owner_id !== $user_id) {

                    // Now, insert a message into the "Messages" table to notify users about the new task.
                    $message_text = 'A new task, ' . $taskname . ', has been added to the project';
                    $insert_message_query = "INSERT INTO Messages (task_id, recipient_id, text, is_newtask_msg) VALUES ('$task_id', '$project_owner_id', '$message_text', '$is_newtask_msg')";

                    // Execute the query to insert the message into the database
                    if (mysqli_query($connection, $insert_message_query)) {
                        echo " ";

                    } else {
                        echo " ";
                    }
                } else {
                    echo " ";
                }

                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => 'New task added successfully'));

            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error adding task section: ' . $connection->error));
            }

        }

    }
}
?>