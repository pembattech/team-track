<?php
session_start();

require_once '../config/connect.php';

// Function to sanitize user inputs
function sanitize_input($input)
{
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = sanitize_input($_POST['project_name']);
    $description = sanitize_input($_POST['description']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);

    // Get the user ID of the logged-in user
    $user_id = $_SESSION['user_id'];
    echo $project_name;

    // Insert project into the Projects table
    $insert_project_query = "INSERT INTO Projects (project_name, description, start_date, end_date)
                            VALUES ('$project_name', '$description', '$start_date', '$end_date')";

    echo $insert_project_query;

    if ($connection->query($insert_project_query)) {
        $project_id = $connection->insert_id;
        echo $project_id;
        // Assign the project to the logged-in user in the ProjectUsers table
        $assign_project_query = "INSERT INTO ProjectUsers (project_id, user_id) VALUES ('$project_id', '$user_id')";
        if ($connection->query($assign_project_query)) {
            echo "Project created and assigned to the logged-in user successfully.";
        } else {
            echo "Error assigning project: " . $connection->error;
        }
    }
} else {
    echo "Error creating project: " . $connection->error;
}
?>