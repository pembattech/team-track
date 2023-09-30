<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
require_once '../../config/connect.php';
include '../utils.php';

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
    // Validate input on the server-side
    $errors = [];

    $project_name = sanitize_input($_POST['project_name']);
    $description = sanitize_input($_POST['description']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);
    $priority = sanitize_input($_POST['priority']);

    if (empty($project_name) || empty($description) || empty($start_date) || empty($end_date) || empty($priority)) {
        $errors[] = "All fields are required.";
    }

    if (!empty($errors)) {
        // Return validation errors
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => implode("<br>", $errors)]);
        exit;
    }

    $background_color = generateRandomColor();

    // Get the user ID of the logged-in user
    $user_id = $_SESSION['user_id'];

    // Insert project into the Projects table
    $insert_project_query = "INSERT INTO Projects (project_name, description, start_date, end_date, priority, background_color)
                           VALUES ('$project_name', '$description', '$start_date', '$end_date', '$priority', '$background_color')";

    if ($connection->query($insert_project_query)) {
        $project_id = $connection->insert_id;

        $assign_projectowner_query = "INSERT INTO ProjectUsers (project_id, user_id, is_projectowner) VALUES ('$project_id', '$user_id', 1)";
        if ($connection->query($assign_projectowner_query)) {
            // Insert a recent activity entry for project creation
            $activity_description = "Project '$project_name' created by user " . getUserName($user_id);
            // Make sure to properly escape and enclose the activity_description
            $activity_description = $connection->real_escape_string($activity_description);

            $insert_activity_query = "INSERT INTO RecentActivity (activity_type, activity_description, project_id) VALUES ('Project Created', '$activity_description', '$project_id')";

            if ($connection->query($insert_activity_query)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => $project_name . " project created successfully"]);
                exit;
            }
        }
    }

    // If any of the queries failed
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => "An error occurred while creating project"]);
}
?>