<?php
session_start();

require_once '../config/connect.php';

// Function to fetch project data based on the project ID
function get_project_data($project_id)
{
    global $connection;

    // Prepare the query with a parameterized statement to prevent SQL injection
    $sql = "SELECT * FROM Projects WHERE project_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $project_id); // "i" indicates the parameter is an integer (project_id)

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the project exists
    if ($result->num_rows > 0) {
        // Fetch the project data
        $project_data = $result->fetch_assoc();
        return $project_data;
    } else {
        // Project not found
        return null;
    }

    // Close the statement and connection
    // $stmt->close();
}

// Check if the project_id parameter is provided
if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
    $project_id = intval($_GET['project_id']);

    // Fetch the project data
    $project_data = get_project_data($project_id);

    // Convert the project data to JSON format
    header('Content-Type: application/json');
    echo json_encode($project_data);
} else {
    // If project_id is not provided or not valid, return an empty JSON object
    echo json_encode([]);
}