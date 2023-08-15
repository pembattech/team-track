<?php
// Start a session to access session variables (if needed)
session_start();

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user ID is set in the session
if (isset($_SESSION['user_id'])) {
    // Get the user ID of the logged-in user
    $user_id = $_SESSION['user_id'];

    // Function to get the IDs of new tasks assigned to the user
    function get_new_task_ids($user_id)
    {
        global $connection;

        $sql = "SELECT task_id FROM Tasks WHERE status != 'Completed' AND user_id = $user_id AND is_new = 1";
        $result = mysqli_query($connection, $sql);

        $newTaskIds = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $newTaskIds[] = $row['task_id'];
        }

        return $newTaskIds;
    }

    // Get the IDs of new tasks assigned to the user
    $newTaskIds = get_new_task_ids($user_id);

    // Set the 'is_new' flag to 0 for these tasks to indicate that they are no longer new
    if (!empty($newTaskIds)) {
        $ids = implode(",", $newTaskIds);
        $sql = "UPDATE Tasks SET is_new = 0 WHERE task_id IN ($ids)";
        mysqli_query($connection, $sql);
    }

    // Return the new task IDs as a JSON array
    echo json_encode($newTaskIds);
} else {
    echo "[]";
}
?>
