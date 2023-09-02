<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
// Replace the database connection code with your actual database connection code
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


session_start();

$user_id = $_SESSION['user_id'];

// Retrieve all messages from the messages table
// SQL query to fetch messages along with the project name for the logged-in recipient_id
$sql = "SELECT Messages.*, Projects.project_name
FROM Messages
LEFT JOIN Tasks ON Messages.task_id = Tasks.task_id
LEFT JOIN ProjectUsers ON Tasks.projectuser_id = ProjectUsers.projectuser_id
LEFT JOIN Projects ON ProjectUsers.project_id = Projects.project_id
WHERE Messages.recipient_id = $user_id
ORDER BY Messages.timestamp DESC;
";

$result = $connection->query($sql);

$messages = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = array(
            'message_id' => $row['message_id'],
            "project_name" => $row["project_name"],
            // 'task_id' => $row['task_id'],
            // 'recipient_id' => $row['recipient_id'],
            // 'sender_id' => $row['sender_id'],
            'text' => $row['text'],
            'timestamp' => $row['timestamp'],
            'is_read' => $row['is_read'],
        );
    }
}

$connection->close();

// Return the messages as JSON
header('Content-Type: application/json');
echo json_encode($messages);
?>