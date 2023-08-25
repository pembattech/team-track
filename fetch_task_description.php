<?php
// Include your database connection or any necessary configuration
include 'config/connect.php'; // Change this to your actual configuration file

// Get the task ID from the URL parameter
$task_id = $_GET['task_id'];

// Fetch the task description from the database based on the task ID
$sql = "SELECT task_description FROM Tasks WHERE task_id = $task_id";
$result = mysqli_query($connection, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $task_description = $row['task_description'];
    echo $task_description;
} else {
    echo "Error fetching task description.";
}
?>

