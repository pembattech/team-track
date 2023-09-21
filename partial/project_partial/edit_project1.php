<?php
require_once '../../config/connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $project_name = $_POST['project_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $project_priority = $_POST["project_priority"];

    // Define an array to store validation errors
    $errors = [];

    // Validate the "project_name" field
    if (empty($project_name)) {
        $errors["project_name"] = "Project name is required.";
    } elseif (strlen($project_name) > 255) {
        $errors["project_name"] = "Project name should be less than 255 characters.";
    }

    // Validate the "description" field
    if (empty($description)) {
        $errors["description"] = "Description is required.";
    } elseif (strlen($description) > 255) {
        $errors["description"] = "Description should be less than 255 characters.";
    }

    // Validate the "start_date" field
    if (empty($start_date)) {
        $errors["start_date"] = "Start date is required.";
    }

    // Validate the "end_date" field
    if (empty($end_date)) {
        $errors["end_date"] = "End date is required.";
    }

    // Validate the "project_priority" field
    if (empty($project_priority)) {
        $errors["project_priority"] = "Project Priority is required.";
    }

    // If there are no validation errors, process the form data
    if (empty($errors)) {
        // Retrieve the existing project data
        $select_project_query = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result = $connection->query($select_project_query);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Check if any fields have been updated
            $updatedFields = [];

            if ($project_name !== $row['project_name']) {
                $updatedFields[] = 'Project Name';
            }

            if ($description !== $row['description']) {
                $updatedFields[] = 'Description';
            }

            if ($start_date !== $row['start_date']) {
                $updatedFields[] = 'Start Date';
            }

            if ($end_date !== $row['end_date']) {
                $updatedFields[] = 'End Date';
            }

            if ($project_priority !== $row['priority']) {
                $updatedFields[] = 'Project Priority';
            }

            // Update project in the Projects table
            $edit_project_query = "UPDATE Projects SET
                            project_name = '$project_name',
                            description = '$description',
                            start_date = '$start_date',
                            end_date = '$end_date',
                            priority = '$project_priority'
                         WHERE project_id = $project_id";

            if ($connection->query($edit_project_query)) {
                $response = [
                    'status' => 'success',
                    'message' => "$project_name Project updated successfully.",
                    'updated_fields' => $updatedFields
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => "Error updating project: " . $connection->error
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Project not found.'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => "Error updating $project_name Project.",
            'errors' => $errors
        ];
    }

    // Return the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
