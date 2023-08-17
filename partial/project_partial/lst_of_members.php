<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teamtrack";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the project_id from the URL or any other source
if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Fetch members of the project
    $sql = "SELECT Users.user_id, Users.username FROM Users
            INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
            WHERE ProjectUsers.project_id = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate options for the select tag
    $selectOptions = "";
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        $username = $row['username'];
        $selectOptions .= "<option value='$user_id'>$username</option>";
    }

    $stmt->close();
}

$connection->close();
?>
