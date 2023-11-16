<?php
// Include your database connection and any necessary functions here
require_once '../../config/connect.php';

include '../utils.php';

// Get the user ID of the logged-in user
session_start();
$user_id = $_SESSION['user_id'];

// Fetch the updated project list
$sql = "SELECT P.project_id, P.project_name, P.background_color 
        FROM Projects P
        INNER JOIN ProjectUsers PU ON P.project_id = PU.project_id
        WHERE PU.user_id = $user_id AND PU.is_projectowner = 1
        ORDER BY P.project_id DESC";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Loop through the results and generate HTML for each project
    while ($row = $result->fetch_assoc()) {
        $project_id = $row['project_id'];
        $project_name = $row['project_name'];
        $background_color = $row['background_color'];

        // Output HTML for each project
        echo '<div class="project-lst">';
        echo '<div class="project-lst-name" style="display: inline-block;">';
        echo '<a href="project.php?project_id=' . $project_id . '" class="project-link">';
        echo '<div class="square" style="background-color:' . $background_color . '"></div>';
        echo '<p class="project-title">' . add_ellipsis($project_name, 65) . '</p>';
        echo '<span class="project_status" style="font-size: 12px; font-weight="300px";>&nbsp;(' . get_project_data($project_id)['status'] . ')</span>';
        echo '</a>';
        echo '</div>';
        if (get_project_owner_id($project_id) == $_SESSION['user_id']) {
            echo '<div class="project-options">';
            echo '<button class="edit-project-btn" id="edit-project-btn" data-project-id="' . $project_id . '">Edit</button>';
            echo '<button class="delete-project-btn" id="delete-project-btn" data-project-id="' . $project_id . '">Delete</button>';
            echo '</div>';
        }
        echo '</div>';
    }
} else {
    // If no projects are assigned, display a message or do something else
    echo '<p>No projects have been created yet.</p>';
}

// Close the database connection
$connection->close();
?>

