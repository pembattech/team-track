// In your project creation logic, after saving the project details to the database
// Notify relevant users by sending a project notification

// Assuming $receiver contains the username(s) of the users to be notified
$subject = "New Project: " . $project_title; // Set the subject of the notification
$body = "A new project has been created: " . $project_title . ".\nPlease check it out!"; // Set the message body
$is_project_notification = 1; // Set the flag to indicate that this is a project notification

// Insert the project notification into the messages table
$query = "INSERT INTO messages (sender, receiver, subject, body, is_project_notification) VALUES ('$username', '$receiver', '$subject', '$body', '$is_project_notification')";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Project created and notification sent successfully!";
} else {
    echo "Error creating project or sending notification: " . mysqli_error($conn);
}

