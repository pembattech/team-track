<!DOCTYPE html>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>View All Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
        }

        .left-side {
            flex: 1;
            padding: 20px;
            border-right: 1px solid #ccc;
        }

        .right-side {
            flex: 2;
            padding: 20px;
        }

        h2 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        .unread-count {
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <div class="left-side message-list">
        <h2>All Messages</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Message ID</th>
                    <th>Project ID</th>
                    <th>Recipient ID</th>
                    <th>Message</th>
                    <th>Read</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Replace the database connection code with your actual database connection code
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "test";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Retrieve all messages from the messages table
                $sql = "SELECT * FROM messages";
                $result = $conn->query($sql);

                $unreadCount = 0;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $message_id = $row['message_id'];
                        $project_id = $row['project_id'];
                        $recipient_id = $row['recipient_id'];
                        $message = $row['message'];
                        $is_read = $row['is_read'] == 1 ? 'Yes' : 'No';

                        echo "<tr data-message-id='$message_id'>";
                        echo "<td>$message_id</td>";
                        echo "<td>$project_id</td>";
                        echo "<td>$recipient_id</td>";
                        echo "<td>$message</td>";
                        echo "<td>$is_read</td>";
                        echo "</tr>";

                        if ($is_read === 'No') {
                            $unreadCount++;
                        }
                    }
                } else {
                    echo "<tr><td colspan='5'>No messages found.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <?php if ($unreadCount > 0): ?>
            <div class="unread-count">
                <?php echo $unreadCount; ?> Unread Messages
            </div>
        <?php endif; ?>
    </div>
    <div class="right-side" id="messageContainer">
        <!-- Selected message content will be displayed here -->
    </div>

    <script>
        // Function to display the message content on the right side when a message is clicked
        function showMessageContent(messageId) {
            // Send an AJAX request to fetch the message content
            $.ajax({
                url: 'get_message.php',
                method: 'GET',
                data: {
                    message_id: messageId
                },
                success: function (response) {
                    // Display the message content in the right-side container
                    $('#messageContainer').html(response);
                    // Mark the message as read using the mark_as_read.php file
                    markMessageAsRead(messageId);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching message content:', error);
                }
            });
        }

        // Function to mark the message as read
        function markMessageAsRead(messageId) {
            // Send an AJAX request to mark the message as read
            $.ajax({
                url: 'mark_as_read.php',
                method: 'POST',
                data: {
                    message_id: messageId
                },
                dataType: 'json',
                success: function (response) {
                    // Update the unread count display with the new unread count
                    if (response.unreadCount > 0) {
                        $('.unread-count').text(response.unreadCount + ' Unread Messages');
                    } else {
                        $('.unread-count').remove();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error marking message as read:', error);
                }
            });
        }

        // Add click event listener to each message row
        $(document).ready(function () {
            $('table tbody tr').click(function () {
                const messageId = $(this).find('td:first-child').text(); // Get the message ID from the first cell
                showMessageContent(messageId);
            });
        });
    </script>
</body>

</html>