<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<?php
require_once '../../config/connect.php';

// Check if the message_id parameter is present
if (isset($_POST['message_id']) && is_numeric($_POST['message_id'])) {
    $message_id = $_POST['message_id'];

    // Retrieve the message from the messages table based on message_id
    $stmt = $connection->prepare("SELECT * FROM Messages WHERE message_id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result_msg = $stmt->get_result();

    if ($result_msg->num_rows > 0) {
        $row = $result_msg->fetch_assoc();
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

        echo $task_id;

        include '../../partial/utils.php';
        if (!empty($task_id)) {
            $projectName = getProjectNameByTaskId($task_id);
        }

        // Mark the message as read
        $sql_update = "UPDATE Messages SET is_read = 1 WHERE message_id = ?";
        $stmt_update = $connection->prepare($sql_update);
        $stmt_update->bind_param("i", $message_id);
        $stmt_update->execute();
        $stmt_update->close();

        if ($is_project_msg) {
            ?>
            <div class="message-format">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>
                            <?php echo $projectName ?>
                        </p>
                        <?php if (!empty($sender_id)) {
                            ?>
                            <p style="font-size: 15px;">From:
                                <?php echo getUserName($sender_id);
                                ?>
                            </p>
                            <?php
                        } ?>
                        <div class="div-space-top"></div>
                        <div class="bottom-line"></div>
                        <div class="div-space-top"></div>
                        <button class="close-message-button button-style">Close</button>
                    </div>
                </div>
                <div class="bottom-line"></div>
                <div class="div-space-top"></div>
                <p>
                    <?php echo $text ?>
                </p>
            </div>
            <?php
        } elseif ($is_newtask_msg) {
            $stmt = $connection->prepare("SELECT * FROM Tasks WHERE task_id = ?");
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the task was found
            if ($result->num_rows > 0) {
                $task = $result->fetch_assoc();
            }
            ?>
            <div class="message-format">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>
                            <?php echo $projectName ?>
                        </p>
                        <?php if (!empty($sender_id)) {
                            ?>
                            <p style="font-size: 15px;">From:
                                <?php echo getUserName($sender_id);
                                ?>
                            </p>
                            <?php
                        } ?>
                        <div class="div-space-top"></div>
                        <div class="bottom-line"></div>
                        <div class="div-space-top"></div>
                        <button class="close-message-button button-style">Close</button>
                    </div>
                </div>
                <div class="bottom-line"></div>
                <div class="div-space-top"></div>

                <div class="get_message approval-form">
                    <form action="/partial/task_partial/update_task.php" method="post">
                        <input type="hidden" name="project_id" value="<?php echo $task['project_id']; ?>">
                        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                        <input class="input-style" type="text" id="editTaskName" name="task_name">
                        <span id="editTaskName-error" class="error-message"></span>
                        <br>

                        <div class="div-space-top"></div>
                        <?php include 'partial/project_partial/lst_of_members.php'; ?>
                        <select id="editAssignee" name="task_assignee" class="select-style">
                            <?php echo $selectOptions; ?>
                        </select>
                        <span id="editAssignee-error" class="error-message"></span>

                        <div class="div-space-top"></div>
                        <div class="textarea-style">
                            <textarea id="editTaskDescription" name="task_description"></textarea>
                        </div>
                        <span id="editTaskDescription-error" class="error-message"></span>

                        <div class="div-space-top"></div>
                        <input class="input-style" type="text" id="editStartDate" name="start_date" placeholder="Start Date"
                            onfocus="(this.type='date')">
                        <span id="editStartDate-error" class="error-message"></span>
                        <br>

                        <div class="div-space-top"></div>
                        <input class="input-style" type="text" id="editEndDate" name="end_date" placeholder="End Date"
                            onfocus="(this.type='date')">
                        <span id="editEndDate-error" class="error-message"></span>
                        <br>

                        <div class="div-space-top"></div>
                        <select id="editStatus" name="status" class="select-style">
                            <option value="" selected disabled hidden>Select a Number</option>
                            <option value="At risk">At risk</option>
                            <option value="Off Track">Off track</option>
                            <option value="On Track">On track</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Blocked">Blocked</option>
                            <option value="Waiting for Approval">Waiting for Approval</option>
                            <option value="In Review">In Review</option>
                        </select>
                        <br>
                        <span id="editStatus-error" class="error-message"></span>

                        <div class="div-space-top"></div>
                        <select id="editPriority" name="priority" class="select-style">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                        <span id="editPriority-error" class="error-message"></span>
                        <br>

                        <div class="div-space-top"></div>
                        <button type="submit" id="submitButton">Save Changes</button>
                    </form>
                </div>
            </div>

            <?php
        } else {
            // Display the message content
            ?>
            <div class="message-format">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>
                            <?php echo $projectName ?>
                        </p>
                        <?php if (!empty($sender_id)) {
                            ?>
                            <p style="font-size: 15px;">From:
                                <?php echo getUserName($sender_id);
                                ?>
                            </p>
                            <?php
                        } ?>
                        <div class="div-space-top"></div>
                        <div class="bottom-line"></div>
                        <div class="div-space-top"></div>
                        <button class="close-message-button button-style">Close</button>
                    </div>
                </div>
                <div class="bottom-line"></div>
                <div class="div-space-top"></div>
                <p>
                    <?php echo $text ?>
                </p>
            </div>
            <?php
        }
        $stmt->close();

    }
}
?>