<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inbox_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();


$username = $_SESSION['username'];

$query = "SELECT * FROM messages WHERE receiver = '$username' ORDER BY timestamp DESC";
$result = mysqli_query($conn, $query);
?>

<!-- Display the messages in a table or any other suitable format -->
<table>
    <tr>
        <th>Sender</th>
        <th>Subject</th>
        <th>Time</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['sender'] . "</td>";
        echo "<td><a href='view_message.php?id=" . $row['id'] . "'>" . $row['subject'] . "</a></td>";
        echo "<td>" . $row['timestamp'] . "</td>";
        echo "</tr>";
    }
    ?>
</table>

<a href="compose.php">Compose New Message</a>

