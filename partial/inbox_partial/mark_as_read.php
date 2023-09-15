<?php

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/connect.php';

// Start the session to access session data
session_start();

if (isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];
    $user_id = $_SESSION['user_id'];

    // Mark the message as "read" in the database
    $updateSql = "UPDATE Messages SET is_read = 1 WHERE message_id = $messageId AND recipient_id = $user_id";
    $result = mysqli_query($connection, $updateSql);

    if ($result) {
        // Query to count unread messages for the user
        $unreadSql = "SELECT COUNT(message_id) AS unread_count FROM Messages
                      WHERE recipient_id = $user_id AND is_read = 0";

        $unreadResult = mysqli_query($connection, $unreadSql);

        if ($unreadResult) {
            $unreadRow = mysqli_fetch_assoc($unreadResult);
            $unreadCount = $unreadRow['unread_count'];

            if ($unreadCount > 0) {
                // Return the updated unread count as JSON response
                header('Content-Type: application/json');
                echo json_encode(['unreadCount' => $unreadCount]);
            } elseif ($unreadCount == 0) {
                // Return the updated unread count as JSON response
                header('Content-Type: application/json');

                echo json_encode(['unreadCount' => $unreadCount]);
            }

        } else {
            echo json_encode(['error' => 'Error counting unread messages.']);
        }
    } else {
        echo json_encode(['error' => 'Error marking message as read.']);
    }
} else {
    echo json_encode(['error' => 'Message ID not provided.']);
}

// Close the database connection
mysqli_close($connection);
?>