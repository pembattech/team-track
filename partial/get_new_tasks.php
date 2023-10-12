<?php
// Start a session to access session variables (if needed)
session_start();

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the user ID of the logged-in user
$user_id = $_SESSION['user_id'];

// Function to get the IDs of new tasks assigned to the user
function get_new_task_ids($user_id)
{
    global $connection;

    $sql = "SELECT task_id FROM Tasks WHERE user_id = $user_id";
    $result = mysqli_query($connection, $sql);

    $newTaskIds = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $newTaskIds[] = $row['task_id'];
    }

    return $newTaskIds;
}

// Get the IDs of new tasks assigned to the user
$newTaskIds = get_new_task_ids($user_id);

// Return the new task IDs as a JSON array
echo json_encode($newTaskIds);
?>