<?php
// Enable error reportin      g
error_reporting(E_ALL);
ini_set('display_errors', 1);


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
    $task_description = sanitize_input($_POST['task_description']);
    $status = "New";
    $user_id = $_SESSION['user_id'];
    $section = "To Do";
    $is_newtask_msg = 1;


    // Check if the 'taskname' field is present and not empty
    if (empty($_POST['taskname'])) {
        $response['status'] = 'error';
        $response['message'] = 'Task Name is required ';

    } elseif (empty($_POST['task_description'])) {
        $response['status'] = 'error';
        $response['message'] = 'Task Description is required';
    } else {
        $project_owner_query = "SELECT * FROM ProjectUsers WHERE project_id = '$project_id' AND is_projectowner='1'";
        $projectuser_result = mysqli_query($connection, $project_owner_query);

        if ($projectuser_result && mysqli_num_rows($projectuser_result) > 0) {
            $projectuser_row = mysqli_fetch_assoc($projectuser_result);
            $projectuser_id = $projectuser_row['projectuser_id'];
            $project_owner_id = $projectuser_row['user_id'];

            echo $project_id;
            echo $projectuser_id;
            echo $project_owner_id;

            $response['status'] = 'success';
            $response['message'] = 'Task added successfully.';
            // Insert the task into the Tasks table
            $insert_project_query = "INSERT INTO Tasks (projectuser_id, task_creator_id, task_name, task_description, status, section) VALUES ('$projectuser_id', '$user_id', '$taskname', '$task_description', '$status', '$section')";

            if ($connection->query($insert_project_query) === TRUE) {
                $task_id = mysqli_insert_id($connection); // Get the last inserted task_id
                echo "Task added successfully. Task ID: " . $task_id . "<br>";

                if ($project_owner_id !== $user_id) {

                    // Now, insert a message into the "Messages" table to notify users about the new task.
                    $message_text = 'A new task, ' . $taskname . ', has been added to the project';
                    echo $message_text;
                    $insert_message_query = "INSERT INTO Messages (task_id, recipient_id, text, is_newtask_msg) VALUES ('$task_id', '$project_owner_id', '$message_text', '$is_newtask_msg')";
                    echo $insert_message_query;

                    // Execute the query to insert the message into the database
                    if (mysqli_query($connection, $insert_message_query)) {
                        echo "Notification message added successfully.";

                    } else {
                        echo "Error inserting notification message: " . mysqli_error($connection);
                        $response['status'] = 'error';
                    }

                    // Send the response back to the client as JSON
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo $user_id, " is the project owner.";
                }

                $response['status'] = 'success';
                $response['message'] = 'Task added successfully.';
                $_SESSION['notification_message'] = "New task added successfully!";

            } else {
                echo "Error adding new task: " . $connection->error;
                $response['status'] = 'error';
                $_SESSION['notification_message'] = "Error adding new task.";
            }

        }
        header("Location: ../project.php?project_id=$project_id");

    }
}
?>