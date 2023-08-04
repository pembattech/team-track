<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teamtrack";

// Check if the task_id parameter is present in the POST request
if (isset($_POST['task_id']) && is_numeric($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    echo $task_id;

    echo "deleting";
    // Set the timezone to your desired timezone
    date_default_timezone_set('UTC');

    // Get the current time and format it as desired (e.g., H:i:s for 24-hour format)
    $current_time = date('H:i:s');

    // Display the current time
    echo "Current time is: " . $current_time;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL query using a prepared statement
    $stmt = $conn->prepare("DELETE FROM Tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        // Task deleted successfully
        echo json_encode(array('status' => 'success', 'message' => 'Task deleted successfully.'));
    } else {
        // Error deleting task
        echo json_encode(array('status' => 'error', 'message' => 'Error deleting task.'));
    }

    $stmt->close();
    $conn->close();
} else {
    // Invalid task ID or no task ID provided
    echo json_encode(array('status' => 'error', 'message' => 'Invalid task ID.'));
}
?>