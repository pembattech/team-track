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
function getUserName($user_id) {
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

?>