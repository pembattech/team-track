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
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve all messages from the messages table
$sql = "SELECT * FROM Messages";
$result = $conn->query($sql);

$messages = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = array(
            'message_id' => $row['message_id'],
            // 'task_id' => $row['task_id'],
            // 'recipient_id' => $row['recipient_id'],
            // 'sender_id' => $row['sender_id'],
            'text' => $row['text'],
            'timestamp' => $row['timestamp'],
            // 'is_read' => $row['is_read'],
        );
    }
}

$conn->close();

// Return the messages as JSON
header('Content-Type: application/json');
echo json_encode($messages);
?>
