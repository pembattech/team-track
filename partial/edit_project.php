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
    $project_id = sanitize_input($_POST['project_id']);
    $project_name = sanitize_input($_POST['project_name']);
    // $description = sanitize_input($_POST['description']);
    // $start_date = sanitize_input($_POST['start_date']);
    // $end_date = sanitize_input($_POST['end_date']);
    // $status = "Not Started";


    // Get the user ID of the logged-in user
    $user_id = $_SESSION['user_id'];
    echo $project_name;

    // Assuming you have the $project_id variable with the specific project's ID

    // Update project in the Projects table
    // $update_project_query = "UPDATE Projects SET
    //                         project_name = '$project_name',
    //                         description = '$description',
    //                         start_date = '$start_date',
    //                         end_date = '$end_date',
    //                         status = '$status',
    //                         background_color = '$background_color'
    //                      WHERE project_id = $project_id";
    // Assuming you have the $project_id variable with the specific project's ID

    // Update project in the Projects table
    $edit_project_query = "UPDATE Projects SET project_name = '$project_name' WHERE project_id = $project_id";


    echo $edit_project_query;

    if ($connection->query($edit_project_query)) {
        // Set a session variable to indicate successful project creation
        // Set a session variable to store the dynamic message
        $_SESSION['notification_message'] = "$project_name Project update successfully.";
        echo "Project update and assigned to the logged-in user successfully.";
        
        // // Redirect to the user's home or desired page after successfullly creating project
        // header("Location: ../profile.php");
    } else {
        echo "Error updating project: " . $connection->error;
        $_SESSION['notification_message'] = "Error updating $project_name Project.";
    }

} else {
    $_SESSION['notification_message'] = "Error updating $project_name Project.";
}
?>