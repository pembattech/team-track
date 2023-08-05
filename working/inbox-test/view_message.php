// view_message.php
<?php
require_once '../../config/connect.php';

if (isset($_GET['id'])) {
    $message_id = $_GET['id'];
    $query = "SELECT * FROM messages WHERE id = $message_id";
    $result = mysqli_query($conn, $query);
    $message = mysqli_fetch_assoc($result);
}
?>

<!-- Display the message details -->
<h2>Subject: <?php echo $message['subject']; ?></h2>
<p>From: <?php echo $message['sender']; ?></p>
<p>Time: <?php echo $message['timestamp']; ?></p>

<?php
if ($message['is_project_notification']) {
    echo "<p>This is a project notification:</p>";
} else {
    echo "<p>This is a regular message:</p>";
}
?>

<p><?php echo $message['body']; ?></p>

