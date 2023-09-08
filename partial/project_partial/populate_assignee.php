<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
include 'config/connect.php';

function populateAssigneeOptions($project_id)
{
    global $connection;

    $task_id = 26;

    // Fetch the users of the specified project
    $sql = "SELECT Users.user_id, Users.username FROM Users
            JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
            WHERE ProjectUsers.project_id = $project_id";

    $result = mysqli_query($connection, $sql);

    if ($result) {
        echo '<select name="assignee" id="editAssignee" class="select-style">'; // Added id attribute

        $is_assigned = true;
        // Check if there's no assignee assigned to the task
        $noAssignee = true;
        // Check if the user assigned to the task is in the project
        $notAssignedUserInProject = true;

        while ($row = mysqli_fetch_assoc($result)) {
            $user_id = $row['user_id'];
            $username = $row['username'];

            // Check if the user is already assigned to the task
            $assigned_sql = "SELECT assignee FROM Tasks WHERE task_id = $task_id";
            $assigned_result = mysqli_query($connection, $assigned_sql);

            if ($assigned_result) {
                $assigned_row = mysqli_fetch_assoc($assigned_result);
                $a = $assigned_row['assignee'];
                echo $user_id;
                echo $a;
                if ($assigned_row['assignee'] == $user_id) {
                    $is_assigned = true;
                    $noAssignee = false;
                    $notAssignedUserInProject = false;
                } elseif (strlen($assigned_row['assignee']) > 0) {
                    $is_assigned = true;
                    $noAssignee = false;
                    $notAssignedUserInProject = true;
                } else {
                    $is_assigned = false;
                }
            }

            // Generate the option tag with "selected" if the user is assigned
            echo '<option value="' . $user_id . '"';
            if ($is_assigned) {
                echo ' selected';
            }
            echo '>' . $username . '</option>';
        }

        // // Add an option for "Select Assignee" if no assignee is found
        // if ($noAssignee) {
        //     echo '<option value="-1" selected >Select Assignee</option>';
        // } elseif ($notAssignedUserInProject) {
        //     echo '<option value="-2" selected >User not found</option>';
        // }

        echo '</select>';
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}

?>