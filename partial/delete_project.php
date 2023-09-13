<?php


require_once '../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access session data
session_start();

// Check if the 'project_id' parameter is present in the URL
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
    $user_id = $_SESSION['user_id'];

    echo $project_id, $user_id;

    $sql_delete_project_users = "DELETE FROM ProjectUsers WHERE project_id = $project_id";

    echo $sql_delete_project_users;

    if ($connection->query($sql_delete_project_users) === FALSE) {
        echo "Error deleting project users: " . $connection->error;
        // Handle the error as needed
    }

    // Delete all tasks associated with the project_id
    $sql_delete_tasks = "DELETE FROM Tasks WHERE project_id = $project_id";
    if ($connection->query($sql_delete_tasks) === FALSE) {
        echo "Error deleting project users: " . $connection->error;
    }

    $delete_project_query = "DELETE FROM Projects WHERE project_id = $project_id";

    if ($connection->query($delete_project_query) === TRUE) {
        echo "Project and its related tasks deleted successfully.";
        $_SESSION['notification_message'] = "Project and its related tasks deleted successfully.";


    } else {
        echo "Error deleting project: " . $connection->error;
        $_SESSION['notification_message'] = "Error deleting project.";

    }

    // Redirect to profile page after processing the form
    header("Location: ../profile.php");

}

?>