<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<?php

// Include necessary PHP files and establish a database connection
require_once '../../config/connect.php';

// Start the session to access session data
session_start();

// Get the user ID of the logged-in user
$user_id = $_SESSION['user_id'];

// Fetch project names from the "Projects" table where the user is assigned
$sql = "SELECT P.project_id, P.project_name, P.background_color 
        FROM Projects P
        INNER JOIN ProjectUsers PU ON P.project_id = PU.project_id
        WHERE PU.user_id = $user_id";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Initialize an empty array to store project items
    $projects = array();

    // Loop through the results and store project items in the array
    while ($row = $result->fetch_assoc()) {
        $project_id = $row['project_id'];
        $project_name = $row['project_name'];
        $background_color = $row['background_color'];

        // Create an array for each project
        $project = array(
            'id' => $project_id,
            'name' => $project_name,
            'color' => $background_color
        );

        // Add the project array to the projects array
        $projects[] = $project;
    }

    // Encode the projects array as JSON and echo it
    echo json_encode($projects);
} else {
    // If no projects are assigned, you can send a custom message as JSON
    echo json_encode(array('message' => 'No projects.'));
}

// Close the database connection
$connection->close();
?>