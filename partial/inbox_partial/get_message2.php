<style>
    .get_message .addtask-popup-content {
        padding: 0;
        margin: 0;
    }
</style>

<?php

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Replace the database connection code with your actual database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teamtrack";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the message_id parameter is present
if (isset($_POST['message_id']) && is_numeric($_POST['message_id'])) {
    $message_id = $_POST['message_id'];

    echo $message_id;

    // Retrieve the message from the messages table based on message_id
    $stmt = $conn->prepare("SELECT * FROM Message WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result_task = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $message_id = $row['message_id'];
        $task_id = $row['task_id'];
        $recipient_id = $row['recipient_id'];
        $sender_id = $row['sender_id'];
        $text = $row['text'];
        $timestamp = $row['timestamp'];
        $is_read = $row['is_read'];
        $is_task_msg = $row['is_task_msg'];
        $is_project_msg = $row['is_project_msg'];
        $is_newtask_msg = $row['is_newtask_msg'];

        // Mark the message as read
        $sql_update = "UPDATE Messages SET is_read = 1 WHERE message_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $message_id);
        $stmt_update->execute();
        $stmt_update->close();


        if ($is_project_msg) {
            echo "This is project message";

        } elseif ($is_newtask_msg) {
            $stmt = $conn->prepare("SELECT * FROM Tasks WHERE task_id = ?");
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the task was found
            if ($result->num_rows > 0) {
                $task = $result->fetch_assoc();
            }
            ?>
            <div class="get_message approval-form">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>Edit Task</p>
                    </div>
                    <div class="bottom-line"></div>
                    <div class="div-space-top"></div>
                    <button type="button" id="deleteButton">Delete Task</button>
                    <div class="div-space-top"></div>
                </div>
                <div class="bottom-line"></div>
                <div class="div-space-top"></div>
                <form method="post" id="editTaskForm" action="partial/task_partial/update_task.php">
                    <input type="hidden" name="project_id" value="<?php echo $task['project_id']; ?>">
                    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                    <label for="editTaskName">Task Name:</label>
                    <input type="text" id="editTaskName" name="task_name" value="<?php echo $task['task_name']; ?>">
                    <br>

                    <label for="editAssignee">Assignee:</label>
                    <textarea id="editAssignee" name="assignee"></textarea>
                    <br>

                    <label for="editTaskDescription">Task Description:</label>
                    <textarea id="editTaskDescription" name="task_description"><?php echo $task['task_description']; ?></textarea>
                    <br>

                    <label for="editStartDate">Start Date:</label>
                    <input type="date" id="editStartDate" name="start_date">
                    <br>

                    <label for="editEndDate">End Date:</label>
                    <input type="date" id="editEndDate" name="end_date">
                    <br>

                    <label for="editStatus">Status:</label>
                    <select id="editStatus" name="status">
                        <option value="At risk">At risk</option>
                        <option value="Off Track">Off track</option>
                        <option value="On Track">On track</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Blocked">Blocked</option>
                        <option value="Pending Approval">Pending Approval</option>
                        <option value="In Review">In Review</option>
                    </select>
                    <br>

                    <label for="editPriority">Priority:</label>
                    <select id="editPriority" name="priority">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                    <br>

                    <button type="submit" id="submitButton">Save Changes</button>
                </form>
            </div>
            </div>
            <?php
            echo "This is new task messaage";


        } else {
            // Display the message content
            echo "<h3>$message_id</h3>";
            echo "<p>$task_id</p>";
            echo "<p>$recipient_id</p>";
            echo "<p>$text</p>";
        }
    } else {
        echo "<p>Message not found.</p>";
    }

    $stmt->close();
}

$conn->close();
?>