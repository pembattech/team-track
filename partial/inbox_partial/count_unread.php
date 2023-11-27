<?php
include '../../config/connect.php';

session_start();
$user_id = $_SESSION['user_id'];

// Query to count unread messages for the user
$unreadSql = "SELECT COUNT(message_id) AS unread_count FROM Messages
WHERE recipient_id = $user_id AND is_read = 0";

$unreadResult = mysqli_query($connection, $unreadSql);

if ($unreadResult) {
    $unreadRow = mysqli_fetch_assoc($unreadResult);
    $unreadCount = $unreadRow['unread_count'];

    // Return the updated unread count as JSON response
    header('Content-Type: application/json');
    echo json_encode(['unreadCount' => $unreadCount]);
} else {
    // Handle the case where the query fails
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error fetching unread count']);
}

?>