<?php
// Start the session to access user data
session_start();

// Function to sanitize user inputs
function sanitize_input($input)
{
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

// Function to get user data from the database
function get_user_data($user_id)
{
    global $connection;

    $user_id = sanitize_input($user_id);

    $sql = "SELECT * FROM Users WHERE user_id = '$user_id'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        return $user_data;
    } else {
        return null;
    }
}

function getProjectNameByTaskId($taskId) {
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

    $project_id = sanitize_input($project_id);

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

    $user_id = sanitize_input($user_id);


    $sql = "SELECT name FROM Users WHERE user_id = $user_id";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Team member"; // Default if user not found
    }
}

function sendNotificationMessage_project_msg($recipientId, $messageText)
{
    global $connection;

    $insert_message_query = "INSERT INTO Messages (recipient_id, text, is_project_msg) VALUES (?, ?, 1)";
    $stmt = $connection->prepare($insert_message_query);
    $stmt->bind_param("is", $recipientId, $messageText);

    if ($stmt->execute()) {
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

function add_ellipsis($string, $aftercount) {
    if (strlen($string) > $aftercount) {
        $string = substr($string, 0, $aftercount) . "...";
    }
    
    return $string;
}
?>