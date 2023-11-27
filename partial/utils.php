<?php
// // Enable error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Function to get user data from the database
function get_user_data($user_id)
{
    global $connection;
    $sql = "SELECT * FROM Users WHERE user_id = '$user_id'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        return $user_data;
    } else {
        return null;
    }
}

function getProjectNameByTaskId($taskId)
{
    global $connection;

    $query = "SELECT Projects.project_name
              FROM Tasks
              INNER JOIN ProjectUsers ON Tasks.projectuser_id = ProjectUsers.projectuser_id
              INNER JOIN Projects ON ProjectUsers.project_id = Projects.project_id
              WHERE Tasks.task_id = $taskId";

    $result = mysqli_query($connection, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $projectName = $row['project_name'];
        mysqli_free_result($result);
        return $projectName;
    } else {
        return "Project";
    }
}

// Function to get project data from the database
function get_project_data($project_id)
{
    global $connection;


    $sql = "SELECT * FROM Projects WHERE project_id = '$project_id'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
        $project_data = mysqli_fetch_assoc($result);
        return $project_data;
    } else {
        return null;
    }
}

// Function to get user's name by user_id
function getUserName($user_id)
{
    global $connection;

    $sql = "SELECT name FROM Users WHERE user_id = $user_id";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Team member"; // Default if user not found
    }
}

function sendNotificationMessage_project_msg($recipientId, $messageText, $project_id)
{
    global $connection;

    // Sanitize the inputs to prevent SQL injection
    $recipientId = intval($recipientId); // Assuming recipientId is an integer
    $messageText = mysqli_real_escape_string($connection, $messageText); // Sanitize the message text
    $project_id = intval($project_id); // Assuming project_id is an integer

    $insert_message_query = "INSERT INTO Messages (recipient_id, text, project_id, is_project_msg) VALUES ($recipientId, '$messageText', $project_id, 1)";

    if ($connection->query($insert_message_query)) {
        return true;
    } else {
        return false;
    }
}


function get_project_owner_id($project_id)
{
    global $connection;

    // Fetch the project owner's user_id
    $sql = "SELECT user_id FROM ProjectUsers WHERE project_id = '$project_id' AND is_projectowner = 1";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $project_owner_id = $row['user_id'];

        return $project_owner_id;
    }


}

function add_ellipsis($string, $aftercount)
{
    if (strlen($string) > $aftercount) {
        $string = substr($string, 0, $aftercount) . "...";
    }

    return $string;
}


// Function to capitalize the first letter of a string
function capitalizeFirstLetter($string)
{
    return ucfirst($string);
}

// Function to capitalize the first letter of each word in a string
function capitalizeEachWord($string)
{
    return ucwords($string);
}

function removeParenthesesWithNumber($string)
{
    $pattern = '/\s*\(\s*\d+\s*\)\s*/';
    return preg_replace($pattern, '', $string);
}

function countTasksBySectionAndProject($section, $projectId)
{
    global $connection;
    // Sanitize user inputs
    $section = $connection->real_escape_string($section);
    $projectId = intval($projectId);

    // SQL query to count tasks
    $sql = "SELECT COUNT(*) as task_count FROM Tasks WHERE section = '$section' AND projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $projectId)";

    // Execute the query
    $result = $connection->query($sql);

    // Check if the query was successful
    if ($result) {
        $row = $result->fetch_assoc();
        $taskCount = $row["task_count"];
        $result->close();
    }

    return $taskCount;
}

function getTaskInfo($task_id)
{
    global $connection;

    // Define an array to store the task information
    $taskInfo = array();

    // Prepare a SQL statement to select the task information
    $select_task_query = "SELECT * FROM Tasks WHERE task_id = ?";
    $select_task_stmt = $connection->prepare($select_task_query);

    if ($select_task_stmt) {
        $select_task_stmt->bind_param("i", $task_id);
        $select_task_stmt->execute();

        // Get the result set
        $result = $select_task_stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the task information as an associative array
            $taskInfo = $result->fetch_assoc();
        }

        $select_task_stmt->close();
    } else {
        // Handle the error if the statement preparation fails
        die("Error preparing select task statement: " . $connection->error);
    }

    return $taskInfo;
}

function getProjectIdByTaskId($taskId)
{
    global $connection;

    // Prepare a SQL statement to select the project ID based on the task ID
    $sql = "SELECT P.project_id
                FROM Projects P
                INNER JOIN ProjectUsers PU ON P.project_id = PU.project_id
                INNER JOIN Tasks T ON PU.projectuser_id = T.projectuser_id
                WHERE T.task_id = ?";

    // Prepare the SQL statement
    $stmt = $connection->prepare($sql);

    if ($stmt) {
        // Bind the task ID parameter
        $stmt->bind_param("i", $taskId);

        // Execute the query
        $stmt->execute();

        // Get the result set
        $result = $stmt->get_result();

        // Check if there are rows in the result set
        if ($result->num_rows > 0) {
            // Fetch the project ID
            $row = $result->fetch_assoc();
            $projectId = $row['project_id'];

            // Close the statement
            $stmt->close();

            // Return the project ID
            return $projectId;
        } else {
            // If no rows are found, return null or handle accordingly
            $stmt->close();
            return null;
        }
    } else {
        // If the statement preparation fails, handle the error (e.g., log or echo the error)
        echo "Error preparing SQL statement: " . $connection->error;
        return null;
    }
}


// Function to add recent activity
function addRecentActivity($user_id, $activity_type, $activity_description, $project_id, $task_id)
{
    global $connection;

    $sql = "INSERT INTO RecentActivity (user_id, activity_type, activity_description, project_id, task_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("issii", $user_id, $activity_type, $activity_description, $project_id, $task_id);

        if ($stmt->execute()) {
            return true; // Success
        } else {
            return false; // Error executing the query
        }
    } else {
        return false; // Error preparing the query
    }
}

function isTaskCreatorOrProjectOwner($taskId, $loggedInUserId)
{
    global $connection;

    // Query to check if the user is the task creator or project owner
    $sql = "SELECT COUNT(*) as count
            FROM Tasks T
            JOIN ProjectUsers PU ON T.projectuser_id = PU.projectuser_id
            WHERE (T.task_creator_id = $loggedInUserId OR PU.is_projectowner = 1)
            AND T.task_id = $taskId";

    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    return false;
}

function getTasksOfAssignedUser($user_id, $project_id, $is_left_project, $msg)
{
    global $connection;

    $sql = "SELECT Tasks.* FROM Tasks
    JOIN ProjectUsers ON Tasks.projectuser_id = ProjectUsers.projectuser_id
    JOIN Users ON Tasks.assignee = Users.user_id
    WHERE Tasks.assignee = $user_id AND ProjectUsers.project_id = $project_id";

    $result = $connection->query($sql);
    $tasks = "";
    $totalTasks = 0;

    if ($result->num_rows > 0) {
        $totalTasks = $result->num_rows;
        if ($is_left_project != '') {
            $user_name = getUserName($user_id);

            $tasks .= $user_name . " " . $msg . ".<br>";
        }

        $tasks .= "Total Tasks Assigned to $user_name: " . $totalTasks . ".<br>Here are the list of task assigned to this user<br><hr>";

        // Fetch data and append to the $tasks variable
        while ($row = $result->fetch_assoc()) {
            $tasks .= "Task Name: " . ($row["task_name"] ? $row["task_name"] : "N/A") . "<br>";
            $tasks .= "Description: " . ($row["task_description"] ? $row["task_description"] : "N/A") . "<br>";
            $tasks .= "Start Date: " . ($row["start_date"] ? $row["start_date"] : "N/A") . "<br>";
            $tasks .= "End Date: " . ($row["end_date"] ? $row["end_date"] : "N/A") . "<br>";
            $tasks .= "Status: " . ($row["status"] ? $row["status"] : "N/A") . "<br>";
            $tasks .= "Priority: " . ($row["priority"] ? $row["priority"] : "N/A") . "<br>";
            $tasks .= "Section: " . ($row["section"] ? $row["section"] : "N/A") . "<br>";

            $tasks .= "<hr>";
        }
    }

    return $tasks;
}

// Function to send a message to the project owner
function sendMessageAboutDeadlineProject($projectId, $msg = NULL)
{
    global $connection;

    // Check if the project has not already sent a deadline message
    $checkIsSendQuery = "SELECT is_send_deadline_msg FROM Projects WHERE project_id = $projectId";
    $checkIsSendResult = $connection->query($checkIsSendQuery);

    if ($checkIsSendResult) {
        $projectData = $checkIsSendResult->fetch_assoc();
        $isSendDeadline = $projectData['is_send_deadline_msg'];

        if ($isSendDeadline == 0) {
            // Get the project owner's user ID
            $ownerQuery = "SELECT user_id FROM ProjectUsers WHERE project_id = $projectId AND is_projectowner = 1";
            $ownerResult = $connection->query($ownerQuery);

            if ($ownerResult->num_rows > 0) {
                $ownerRow = $ownerResult->fetch_assoc();
                $ownerUserId = $ownerRow['user_id'];

                // Create a message
                $messageText = "The deadline for your project is approaching! Your project, which you oversee, is set to conclude in 5 days." . $msg;
                // Insert the message into the Messages table
                $insertMessageQuery = "INSERT INTO Messages (text, recipient_id, is_project_msg, project_id)
                                       VALUES ('$messageText', $ownerUserId, 1, $projectId)";

                if ($connection->query($insertMessageQuery)) {
                    // If the message is inserted successfully, update is_send_deadline_msg to 1
                    $updateIsSendQuery = "UPDATE Projects SET is_send_deadline_msg = 1 WHERE project_id = $projectId";
                    $connection->query($updateIsSendQuery);
                }
            }
        }
    }
}


function sendMessageAboutDeadlineTask($taskId, $projectId)
{
    global $connection;

    // Check if the task has not already sent a deadline message
    $checkIsSendQuery = "SELECT is_send_deadline_msg FROM Tasks WHERE task_id = $taskId";
    $checkIsSendResult = $connection->query($checkIsSendQuery);

    if ($checkIsSendResult) {
        $taskData = $checkIsSendResult->fetch_assoc();
        $isSendDeadline = $taskData['is_send_deadline_msg'];

        if ($isSendDeadline == 0) {
            // Get the project owner's user ID
            $ownerQuery = "SELECT user_id FROM ProjectUsers WHERE project_id = $projectId AND is_projectowner = 1";
            $ownerResult = $connection->query($ownerQuery);

            if ($ownerResult->num_rows > 0) {
                $ownerRow = $ownerResult->fetch_assoc();
                $ownerUserId = $ownerRow['user_id'];

                // Define an array to store the task information
                $taskInfo = array();

                // Prepare a SQL statement to select the task information
                $select_task_query = "SELECT * FROM Tasks WHERE task_id = ?";
                $select_task_stmt = $connection->prepare($select_task_query);

                if ($select_task_stmt) {
                    $select_task_stmt->bind_param("i", $taskId);
                    $select_task_stmt->execute();

                    // Get the result set
                    $result = $select_task_stmt->get_result();

                    if ($result->num_rows > 0) {
                        // Fetch the task information as an associative array
                        $taskInfo = $result->fetch_assoc();

                        // Assign variables to each key
                        $task_id = $taskInfo['task_id'];
                        $projectuser_id = $taskInfo['projectuser_id'];
                        $task_creator_id = $taskInfo['task_creator_id'];
                        $task_name = $taskInfo['task_name'];
                        $assignee = $taskInfo['assignee'];
                        $task_description = $taskInfo['task_description'];
                        $start_date = $taskInfo['start_date'];
                        $end_date = $taskInfo['end_date'];
                        $status = $taskInfo['status'];
                        $section = $taskInfo['section'];
                        $priority = $taskInfo['priority'];
                        $is_send_deadline_msg = $taskInfo['is_send_deadline_msg'];

                    }

                    $select_task_stmt->close();
                }

                // Create a message
                $messageText = "The deadline for the task, " . $task_name . ", is approaching! The task you assigned is set to conclude in 5 days.";
                // Insert the message into the Messages table
                $insertMessageQuery_assignee = "INSERT INTO Messages (text, recipient_id, is_task_msg, task_id)
                                        VALUES ('$messageText', $assignee, 1, $taskId)";

                if ($connection->query($insertMessageQuery_assignee)) {

                    if ($assignee != $ownerUserId) {
                        // Insert the message into the Messages table
                        $insertMessageQuery = "INSERT INTO Messages (text, recipient_id, is_task_msg, task_id)
                                        VALUES ('$messageText', $ownerUserId, 1, $taskId)";

                        $connection->query($insertMessageQuery);
                    }


                    // If the message is inserted successfully, update is_send_deadline_msg to 1
                    $updateIsSendQuery = "UPDATE Tasks SET is_send_deadline_msg = 1 WHERE task_id = $task_id";
                    $connection->query($updateIsSendQuery);
                }

            }
        }
    }
}



?>