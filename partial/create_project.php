<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please log in first.");
}

require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = $_POST['project_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $end_date = 'Planning';

    // Get the user ID of the logged-in user
    $user_id = $_SESSION['user_id'];

    // Insert project into the Projects table
    $insert_project_query = "INSERT INTO Projects (project_name, description, start_date, end_date)
                            VALUES ('$project_name', '$description', '$start_date', '$end_date')";

    if ($connection->query($insert_project_query) === TRUE) {
        $project_id = $connection->insert_id;

        // Assign the project to the logged-in user in the ProjectUsers table
        $assign_project_query = "INSERT INTO ProjectUsers (project_id, user_id) VALUES ('$project_id', '$user_id')";

        if ($connection->query($assign_project_query) === TRUE) {
            echo "Project created and assigned to the logged-in user successfully.";
        } else {
            echo "Error assigning project: " . $connection->error;
        }
    } else {
        echo "Error creating project: " . $connection->error;
    }
}

$connection->close();
?>
