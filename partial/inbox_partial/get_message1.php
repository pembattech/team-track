<?php

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Check if the message_id parameter is present
if (isset($_POST['message_id']) && is_numeric($_POST['message_id'])) {
    $message_id = $_POST['message_id'];

    echo $message_id;

    // Retrieve the message from the messages table based on message_id
    $sql = "SELECT * FROM Messages WHERE message_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $message_id = $row['message_id'];
        $task_id = $row['task_id'];
        $recipient_id = $row['recipient_id'];
        $sender_id = $row['sender_id'];
        $text = $row['text'];
        $timestamp = $row['timestamp'];
        $is_read = $row['is_read'];

        // Mark the message as read
        $sql_update = "UPDATE Messages SET is_read = 1 WHERE message_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $message_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Display the message content
        echo "<h3>$message_id</h3>";
        echo "<p>$task_id</p>";
        echo "<p>$recipient_id</p>";
        echo "<p>$text</p>";
    } else {
        echo "<p>Message not found.</p>";
    }

    $stmt->close();
}

$conn->close();
?>