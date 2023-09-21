<?php

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

require_once '../config/connect.php';

function generateRandomColor()
{
    $colors = ["#E57373", "#F06292", "#BA68C8", "#9575CD", "#7986CB", "#64B5F6", "#4FC3F7", "#81C784", "#AED581", "#FFF176", "#FFB74D", "#FF8A65", "#A1887F", "#E0E0E0"];
    $randomIndex = array_rand($colors);
    return $colors[$randomIndex];
}

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
    $priority = sanitize_input($_POST['priority']);
    $background_color = generateRandomColor();


    // Get the user ID of the logged-in user
    $user_id = $_SESSION['user_id'];
    echo $project_name;

    // Insert project into the Projects table
    $insert_project_query = "INSERT INTO Projects (project_name, description, start_date, end_date, $priority, background_color)
                               VALUES ('$project_name', '$description', '$start_date', '$end_date', '$priority', '$background_color')";

    echo $insert_project_query;

    if ($connection->query($insert_project_query)) {
        $project_id = $connection->insert_id;
        echo $project_id;
        // Assign the project to the logged-in user in the ProjectUsers table
        $assign_project_query = "INSERT INTO ProjectUsers (project_id, user_id, is_projectowner) VALUES ('$project_id', '$user_id', 1)";
        if ($connection->query($assign_project_query)) {
            // Set a session variable to indicate successful project creation
            // Set a session variable to store the dynamic message
            $_SESSION['notification_message'] = " $project_name Project created successfully.";

            echo "Project created and assigned to the logged-in user successfully.";

            // Redirect to the user's home or desired page after successfullly creating project
            header("Location: ../home.php");
        } else {
            echo "Error assigning project: " . $connection->error;
        }
    }
} else {
    echo "Error creating project: " . $connection->error;
}
?>