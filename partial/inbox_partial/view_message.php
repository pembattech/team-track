<!DOCTYPE html>
<html>

<head>
    <title>View Message</title>
</head>

<body>
    <?php
    // Replace the database connection code with your actual database connection code
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "treamtrack";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the message_id parameter is present in the URL
    if (isset($_GET['message_id']) && is_numeric($_GET['message_id'])) {
        $message_id = $_GET['message_id'];

        // Prepare and execute the SQL query using a prepared statement
        $stmt = $conn->prepare("SELECT * FROM messages WHERE message_id = ?");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the message exists
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $task_id = $row['task_id'];
            $recipient_id = $row['recipient_id'];
            $message = $row['message'];
            $is_read = $row['is_read'] == 1 ? 'Yes' : 'No';

            // Display the message content
            echo "<h2>Message ID: $message_id</h2>";
            echo "<p><strong>Project ID:</strong> $task_id</p>";
            echo "<p><strong>Recipient ID:</strong> $recipient_id</p>";
            echo "<p><strong>Message:</strong></p>";
            echo "<p>$message</p>";
            echo "<p><strong>Read:</strong> $is_read</p>";

            // If the message is not already read, update the 'is_read' field to 'Yes'
            if ($is_read === 'No') {
                $updateStmt = $conn->prepare("UPDATE messages SET is_read = '1' WHERE message_id = ?");
                $updateStmt->bind_param("i", $message_id);
                $updateStmt->execute();
                $updateStmt->close();
                // Update the local variable as well to reflect the change
                $is_read = 'Yes';
            }
        } else {
            echo "<p>Message not found.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Invalid message ID.</p>";
    }

    $conn->close();
    ?>
</body>

</html>
