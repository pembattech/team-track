<?php

include 'config/connect.php';

// Function to generate the priority select dropdown
function projectPrioritySelect($projectId)
{
    global $connection;

    // Query to fetch available priorities for the specified project ID
    $query = "SELECT DISTINCT priority FROM Projects WHERE project_id = ?";

    // Prepare the query
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();

    // Define the $priority variable
    $priority = null;

    // Bind the result
    $stmt->bind_result($priority);

    // Fetch the priorities into an array
    $priorities = array();
    while ($stmt->fetch()) {
        $priorities[] = strtolower($priority);
    }

    // Close the statement
    $stmt->close();

    // HTML code to generate the select tag with options
    echo '<select class="select-style" id="project_priority" name="project_priority">';
    echo '<option value="" ' . (empty($priorities) ? 'selected' : '') . ' hidden>Select Priority</option>';
    echo '<option value="low" ' . (in_array("low", $priorities) ? 'selected' : '') . '>Low</option>';
    echo '<option value="medium" ' . (in_array("medium", $priorities) ? 'selected' : '') . '>Medium</option>';
    echo '<option value="high" ' . (in_array("high", $priorities) ? 'selected' : '') . '>High</option>';
    echo '</select>';
}

?>