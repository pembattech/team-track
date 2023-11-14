<?php

include 'config/connect.php';

// Function to generate the status select dropdown
function projectStatusSelect($projectId)
{
    global $connection;

    // Query to fetch available status_ for the specified project ID
    $query = "SELECT DISTINCT status FROM Projects WHERE project_id = ?";

    // Prepare the query
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();

    // Define the $status variable
    $status = null;

    // Bind the result
    $stmt->bind_result($status);

    // Fetch the status_ into an array
    $status_ = array();
    while ($stmt->fetch()) {
        $status_[] = strtolower($status);
    }

    // Close the statement
    $stmt->close();

    // HTML code to generate the select tag with options
    echo '<select class="select-style" id="project_status" name="project_status">';
    echo '<option value="" ' . (empty($status_) ? 'selected' : '') . ' hidden>Select Status</option>';
    echo '<option value="active" ' . (in_array("active", $status_) ? 'selected' : '') . '>Active</option>';
    echo '<option value="canceled" ' . (in_array("canceled", $status_) ? 'selected' : '') . '>Canceled</option>';
    echo '<option value="complete" ' . (in_array("complete", $status_) ? 'selected' : '') . '>Complete</option>';
    echo '</select>';
}

?>