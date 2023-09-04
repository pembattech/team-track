<?php
require_once '../../config/connect.php';

$user_id = $_SESSION['user_id'];

// Query to count unread messages for the user
$sql = "SELECT COUNT(message_id) AS unread_count FROM Messages
        WHERE recipient_id = $user_id AND is_read = 0";

$result = mysqli_query($connection, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $unreadCount = $row['unread_count'];

    echo 'Total Unread Messages: ' . $unreadCount;
} else {
    echo 'Error: ' . mysqli_error($connection);
}

// Close the database connection
mysqli_close($connection);
?>
