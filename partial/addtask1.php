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
        $project_owner_query = "SELECT * FROM ProjectUsers WHERE project_id = '$project_id' AND is_projectowner='1'";
        $result = mysqli_query($connection, $project_owner_query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            echo $project_id;
            echo $row['user_id'];

            $response['status'] = 'success';
            $response['message'] = 'Task added successfully.';
            $insert_project_query = "INSERT INTO Tasks (project_id, user_id, task_name, status, section) VALUES ('$project_id', '$user_id', '$taskname', '$status', '$section')";

            if ($connection->query($insert_project_query) === TRUE) {
                $task_id = mysqli_insert_id($connection); // Get the last inserted task_id
                echo "Task added successfully. Task ID: " . $task_id . "<br>";

                // Now, insert a message into the "Messages" table to notify users about the new task.
                $message_text = "A new task, '" . $taskname . "', has been added to the project. Status: " . $status . ", Section: " . $section . ".";
                $insert_message_query = "INSERT INTO Messages (task_id, sender_id, text) 
                                    VALUES ('$task_id', '$user_id', '$message_text')";

                // Execute the query to insert the message into the database
                if (mysqli_query($connection, $insert_message_query)) {
                    echo "Notification message added successfully.";

                    $response['status'] = 'success';
                    $response['message'] = 'Task added successfully.';
                    $_SESSION['notification_message'] = "New task added successfully!";

                } else {
                    echo "Error inserting notification message: " . mysqli_error($connection);
                    $response['status'] = 'error';

                }
                // Send the response back to the client as JSON
                header('Content-Type: application/json');
                echo json_encode($response);
                
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