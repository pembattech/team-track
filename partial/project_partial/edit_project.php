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
    $project_status = $_POST["project_status"];

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

    if (empty($project_status)) {
        $errors["project_status"] = "Project status is required.";
    }


    // If there are no validation errors, process the form data
    if (empty($errors)) {
        // Retrieve the existing project data
        $select_project_query = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result = $connection->query($select_project_query);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Initialize an array to store detailed changes
            $detailedChanges = [];

            // Check if any fields have been updated
            if ($project_name !== $row['project_name']) {
                $detailedChanges[] = 'Project Name changed from "' . $row['project_name'] . '" to "' . $project_name . '"';
            }

            if ($description !== $row['description']) {
                $detailedChanges[] = 'Description changed from "' . $row['description'] . '" to "' . $description . '"';
            }

            if ($start_date !== $row['start_date']) {
                $detailedChanges[] = 'Start Date changed from "' . $row['start_date'] . '" to "' . $start_date . '"';
            }

            if ($end_date !== $row['end_date']) {
                $detailedChanges[] = 'End Date changed from "' . $row['end_date'] . '" to "' . $end_date . '"';
            }

            if ($project_priority !== $row['priority']) {
                $detailedChanges[] = 'Project Priority changed from "' . $row['priority'] . '" to "' . $project_priority . '"';
            }

            if ($project_status !== $row['status']) {
                $detailedChanges[] = 'Project status changed from "' . $row['status'] . '" to "' . $project_status . '"';
            }


            // Update project in the Projects table
            $edit_project_query = "UPDATE Projects SET
                    project_name = '$project_name',
                    description = '$description',
                    start_date = '$start_date',
                    end_date = '$end_date',
                    priority = '$project_priority',
                    status = '$project_status'
                 WHERE project_id = $project_id";

            if ($connection->query($edit_project_query)) {

                // Query to fetch the latest project details
                $sql_project = "SELECT * FROM Projects WHERE project_id = $project_id";
                $latest_project_details_query = mysqli_query($connection, $sql_project);

                if ($latest_project_details_query) {
                    $latest_project_details = mysqli_fetch_assoc($latest_project_details_query);
                    $response = [
                        'status' => 'success',
                        'message' => "$project_name Project updated successfully.",
                        'updated_fields' => $detailedChanges,
                        'latest_project_details' => $latest_project_details,
                    ];

                }

                // Send a message to all project members about the update
                $message = 'Project update: The following changes have been made in the project "' . $project_name . '": ' . implode('', $detailedChanges);
                $projectMembersQuery = "SELECT user_id FROM ProjectUsers WHERE project_id = $project_id";
                $membersResult = $connection->query($projectMembersQuery);

                while ($member = $membersResult->fetch_assoc()) {
                    $recipientId = $member['user_id'];
                    // Insert the message into the Messages table for each project member
                    $insertMessageQuery = "INSERT INTO Messages (text, recipient_id, is_project_msg, project_id) VALUES ('$message', $recipientId, 1, $project_id)";
                    $connection->query($insertMessageQuery);
                }
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
?>