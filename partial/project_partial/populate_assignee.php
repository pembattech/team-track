<?php
include 'config/connect.php';

function populateAssigneeOptions($project_id)
{
    global $connection;

    $task_id = 0;

    // Fetch the users of the specified project
    $sql = "SELECT Users.user_id, Users.username FROM Users
            JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
            WHERE ProjectUsers.project_id = $project_id";

    $result = mysqli_query($connection, $sql);

    if ($result) {
        echo '<select name="assignee" id="editAssignee" class="select-style">'; // Added id attribute

        // Check if there's no assignee assigned to the task
        $noAssignee = true;

        while ($row = mysqli_fetch_assoc($result)) {
            $user_id = $row['user_id'];
            $username = $row['username'];

            // Check if the user is already assigned to the task
            $assigned_sql = "SELECT assignee FROM Tasks WHERE task_id = $task_id";
            $assigned_result = mysqli_query($connection, $assigned_sql);

            if ($assigned_result) {
                $assigned_row = mysqli_fetch_assoc($assigned_result);
                if ($assigned_row['assignee'] == $user_id) {
                    $is_assigned = true;
                    $noAssignee = false;
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
        echo $noAssignee;
        // Add an option for "Select Assignee" if no assignee is found
        if ($noAssignee) {
            echo '<option value="-1" selected hidden >Select Assignee</option>';
        }

        echo '</select>';
    } else {
        echo 'Error: ' . mysqli_error($connection);
    }
}

?>
