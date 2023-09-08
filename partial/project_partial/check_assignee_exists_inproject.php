<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
include '../../config/connect.php';

function check_assignee_exists_inproject($project_id, $task_id)
{
    global $connection;

    // Initialize a variable to store the result
    $result = false;

    // Fetch the users of the specified project
    $sql = "SELECT Users.user_id, Users.username FROM Users
            JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
            WHERE ProjectUsers.project_id = $project_id";

    $fetchResult = mysqli_query($connection, $sql);

    if ($fetchResult) {

        while ($row = mysqli_fetch_assoc($fetchResult)) {
            $user_id = $row['user_id'];
            $username = $row['username'];

            // Check if the user is already assigned to the task
            $assigned_sql = "SELECT assignee FROM Tasks WHERE task_id = $task_id";
            $assigned_result = mysqli_query($connection, $assigned_sql);

            if ($assigned_result) {
                $assigned_row = mysqli_fetch_assoc($assigned_result);
                $assigne_user = $assigned_row['assignee'];

                if ($user_id == $assigne_user) {
                    // User ID matches the assignee, set the result to true
                    $result = true;
                }
            }
        }
    }

    // Create an array with the result
    $response = array('result' => $result);

    // Return the task details as a JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}

if (isset($_GET['task_id']) && isset($_GET['project_id'])) {
    $task_id = $_GET['task_id'];
    $project_id = $_GET['project_id'];

    check_assignee_exists_inproject($project_id, $task_id);
}
?>
