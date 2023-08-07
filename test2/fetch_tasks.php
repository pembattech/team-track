<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'project_id' parameter is present in the request
if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Prepare and execute the SQL query using a prepared statement
    $stmt = $conn->prepare("SELECT t.*, u.username AS assignee_name FROM Tasks t
                           LEFT JOIN Users u ON t.assignee = u.user_id
                           WHERE t.project_id = ?");
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

$conn->close();

// Return the tasks data as JSON
header('Content-Type: application/json');
echo json_encode($tasksBySection);

