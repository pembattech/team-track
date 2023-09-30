<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];

    $sql = "SELECT * FROM RecentActivity WHERE project_id = '$project_id' ORDER BY activity_date DESC";
    $result = mysqli_query($connection, $sql);

    $activities = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Add the 'datetime' field to the JSON data
            $row['datetime'] = $row['activity_date'];
            $activities[] = $row;
        }
        mysqli_free_result($result);
    }
}

// Return activities as JSON
header('Content-Type: application/json');
echo json_encode($activities);
?>