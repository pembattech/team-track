<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../config/connect.php';
include '../utils.php';
session_start();
$userId = $_SESSION['user_id'];

// Query to fetch end dates of tasks assigned to the user, exactly 5 days from today
$sql = "SELECT task_id, task_name, end_date, assignee FROM Tasks 
        WHERE assignee = $userId AND end_date = DATE_ADD(CURDATE(), INTERVAL 5 DAY)";

$result = $connection->query($sql);

$response = array();

if ($result->num_rows > 0) {
    // Store data for each task in the response array
    while ($row = $result->fetch_assoc()) {
        $task = array(
            'task_id' => $row["task_id"],
            'task_name' => $row["task_name"],
            'end_date' => $row["end_date"],
            'assignee' => $row["assignee"]
        );
        $response[] = $task;

        $project_id = getProjectIdByTaskId($row['task_id']);

        sendMessageAboutDeadlineTask($row['task_id'], $project_id);

    }
} else {
    $response['message'] = "No tasks found for the user ending exactly 5 days from today.";
}

// Return the response in JSON format
header('Content-Type: application/json');
echo json_encode($response);


?>