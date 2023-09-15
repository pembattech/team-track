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
$sql = "SELECT 
M.*,
COALESCE(P1.project_name, P2.project_name) AS project_name
FROM Messages M
LEFT JOIN Tasks T ON M.task_id = T.task_id
LEFT JOIN ProjectUsers PU1 ON T.projectuser_id = PU1.projectuser_id
LEFT JOIN Projects P1 ON PU1.project_id = P1.project_id
LEFT JOIN Projects P2 ON M.project_id = P2.project_id
WHERE M.recipient_id = $user_id
ORDER BY M.timestamp DESC;

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