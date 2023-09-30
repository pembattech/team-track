<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../config/connect.php';
include 'utils.php';

// Start the session to access session data
session_start();

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
        $project_owner_query = "SELECT * FROM ProjectUsers WHERE project_id = ? AND is_projectowner='1'";
        $stmt = mysqli_prepare($connection, $project_owner_query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $project_id);
            mysqli_stmt_execute($stmt);
            $projectuser_result = mysqli_stmt_get_result($stmt);

            if ($projectuser_result && mysqli_num_rows($projectuser_result) > 0) {
                $projectuser_row = mysqli_fetch_assoc($projectuser_result);
                $projectuser_id = $projectuser_row['projectuser_id'];
                $project_owner_id = $projectuser_row['user_id'];

                $insert_project_query = "INSERT INTO Tasks (projectuser_id, task_creator_id, task_name, task_description, status, section) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection, $insert_project_query);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "iissss", $projectuser_id, $user_id, $taskname, $task_description, $status, $section);
                    if (mysqli_stmt_execute($stmt)) {
                        $task_id = mysqli_insert_id($connection);

                        $task_creator_name = getUserName($user_id);

                        $activity_description = "Task '$taskname' added to project by user '$task_creator_name'";
                        $insert_activity_query = "INSERT INTO RecentActivity (activity_type, activity_description, project_id) VALUES (?, ?, ?)";
                        $stmt = mysqli_prepare($connection, $insert_activity_query);

                        if ($stmt) {
                            $activity_type = "Task Created";
                            mysqli_stmt_bind_param($stmt, "ssi", $activity_type, $activity_description, $project_id);
                            if (mysqli_stmt_execute($stmt)) {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => 'New task added successfully'));
                            } else {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'error', 'message' => 'Error adding task section: '));
                            }
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'error', 'message' => 'Error preparing activity insertion query: '));
                        }
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => 'Error adding task: '));
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'Error preparing task insertion query: '));
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Project owner not found or user is not the owner.'));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Error preparing project owner query: '));
        }
    }
}
?>