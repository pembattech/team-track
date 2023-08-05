<?php
// update_task.php

// ... (your existing code) ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (your existing code) ...

    // Perform the update query to update the task
    $updateQuery = "UPDATE Tasks SET task_name=?, task_description=?, assignee=?, start_date=?, end_date=?, status=?, priority=? WHERE task_id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssisi", $taskName, $taskDescription, $assignee, $startDate, $endDate, $status, $priority, $taskId);

    if ($stmt->execute()) {
        // Task updated successfully
        $stmt->close();
        $conn->close();

        // Send a notification to the project owner (assuming project_owner_id is available)
        $project_owner_id = 1; // Replace 1 with the actual project owner's user ID
        $notificationMessage = "A task has been updated in your project.";

        // Create a notification message in the messages table
        createNotificationMessage($project_id, $project_owner_id, $notificationMessage);
    } else {
        // Handle the error if needed
        echo "Error updating task: " . $conn->error;
        $stmt->close();
        $conn->close();
    }
}

function createNotificationMessage($project_id, $recipient_id, $message) {
    // Establish database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "test";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query to insert the notification message
    $stmt = $conn->prepare("INSERT INTO messages (project_id, recipient_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $project_id, $recipient_id, $message);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
