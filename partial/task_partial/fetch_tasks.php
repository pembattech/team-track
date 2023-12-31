<?php
require_once '../../config/connect.php';

// Check if the 'project_id' parameter is present in the request
if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Prepare and execute the SQL query using a prepared statement
    $stmt = $connection->prepare("
    SELECT 
        t.*, 
        u.username AS assignee_name,
        tc.username AS task_creator_name
    FROM 
        Tasks t 
    LEFT JOIN 
        Users u ON t.assignee = u.user_id 
    LEFT JOIN 
        ProjectUsers pu ON t.projectuser_id = pu.projectuser_id 
    LEFT JOIN
        Users tc ON t.task_creator_id = tc.user_id
    WHERE 
        pu.project_id = ?
");

    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Store tasks grouped by section
    $tasksBySection = array(
        "To Do" => array(),
        "Doing" => array(),
        "Done" => array()
    );

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $section = $row['section'];
            if (!isset($tasksBySection[$section])) {
                $tasksBySection[$section] = array();
            }
            $tasksBySection[$section][] = $row;
        }
    }

    $stmt->close();
}

$connection->close();

// Return the tasks data as JSON
header('Content-Type: application/json');
echo json_encode(array('tasksBySection' => $tasksBySection, 'project_id' => $project_id));