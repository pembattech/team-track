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

    $task_id = isset($_GET["task_id"]) ? $_GET["task_id"] : null;
    $assignee_id = null;

    if ($task_id !== null) {
        $query = "SELECT Tasks.*, Users.user_id, Users.username FROM Tasks
              LEFT JOIN Users ON Tasks.assignee = Users.user_id
              WHERE task_id = $task_id";

        $result = mysqli_query($connection, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $task = mysqli_fetch_assoc($result);

                // Extract the assignee information
                $assignee_id = $task['assignee']; // Assuming the user_id column holds the assignee's ID

            } else {
                echo "Task not found";
            }
        }

    }

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

        // Check if the current user is the assignee
        $selected = ($user_id === $assignee_id) ? "selected" : "";

        $selectOptions .= "<option value='$user_id' $selected>$username</option>";
    }

    $stmt->close();
}

$connection->close();
?>

