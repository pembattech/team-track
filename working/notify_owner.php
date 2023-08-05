<?php
// notify_owner.php

// Establish database connection
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_db_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all unread messages for the project owner (assuming project_owner_id is available)
$project_owner_id = 1; // Replace 1 with the actual project owner's user ID
$query = "SELECT * FROM messages WHERE recipient_id = $project_owner_id AND `read` = FALSE ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Notify the project owner for each new message and mark them as read
    while ($row = $result->fetch_assoc()) {
        $message_id = $row['message_id'];
        $message = $row['message'];

        // Notify the project owner using your preferred notification method (e.g., email, SMS, etc.)
        // For demonstration purposes, let's just print the messages
        echo "New Message (ID: $message_id): $message <br>";

        // Mark the message as read
        $updateQuery = "UPDATE messages SET `read` = TRUE WHERE message_id = $message_id";
        $conn->query($updateQuery);
    }
}

$conn->close();

