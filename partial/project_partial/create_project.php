<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
require_once '../../config/connect.php';

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
    $errors = array();

    $project_name = sanitize_input($_POST['project_name']);
    if (empty($project_name)) {
        $errors[] = "Project Name is required.";
    }

    $description = sanitize_input($_POST['description']);
    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    $start_date = sanitize_input($_POST['start_date']);
    if (empty($start_date)) {
        $errors[] = "Start Date is required.";
    }

    $end_date = sanitize_input($_POST['end_date']);
    if (empty($end_date)) {
        $errors[] = "End Date is required.";
    }

    $priority = sanitize_input($_POST['priority']);
    if (empty($priority)) {
        $errors[] = "Priority is required.";
    }

    if (!empty($errors)) {
        // Return validation errors
        echo implode("<br>", $errors);
    } else {
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
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => $project_name . " project created successfully"));

            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => "An error occured while creating project"));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => "An error occured while creating project"));
        }
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => "An error occured while creating project"));
}
?>