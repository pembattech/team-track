<?php
include '../../config/connect.php';
include '../utils.php';


// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$userId = $_SESSION['user_id'];


// Query to fetch end dates of projects created by the user, exactly 5 days from today
$sql = "SELECT project_id, project_name, end_date FROM Projects 
        WHERE project_id IN (SELECT project_id FROM ProjectUsers WHERE user_id = $userId AND is_projectowner = 1)
        AND end_date = DATE_ADD(CURDATE(), INTERVAL 5 DAY)";

$result = $connection->query($sql);

$response = array();

if ($result->num_rows > 0) {
    // Store data for each project in the response array
    while ($row = $result->fetch_assoc()) {
        $project = array(
            'project_id' => $row["project_id"],
            'project_name' => $row["project_name"],
            'end_date' => $row["end_date"]
        );
        $response[] = $project;

        // Send a message to the project owner
        sendMessageAboutDeadlineProject($row["project_id"], 'The deadline date is ' . $row["end_date"]);
    }
} else {
    $response['message'] = "No projects found for the user ending exactly 5 days from today.";
}

// Return the response in JSON format
header('Content-Type: application/json');
echo json_encode($response);

?>