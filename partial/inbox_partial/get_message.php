<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<?php
require_once '../../config/connect.php';

session_start();


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
        $project_id = $row['project_id'];
        $recipient_id = $row['recipient_id'];
        $sender_id = $row['sender_id'];
        $text = $row['text'];
        $timestamp = $row['timestamp'];
        $is_read = $row['is_read'];
        $is_task_msg = $row['is_task_msg'];
        $is_project_msg = $row['is_project_msg'];
        $is_newtask_msg = $row['is_newtask_msg'];

        include '../../partial/utils.php';
        if (!empty($task_id)) {
            $projectName = getProjectNameByTaskId($task_id);
        } else {
            $projectName = get_project_data($project_id)['project_name'];
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
                    <?php
                    $projectId = getProjectIdByTaskId($task_id);
                    $project_owner_id = get_project_owner_id($projectId);
                    ?>

                    <!-- <form action="partial/task_partial/update_task.php" method="post"> -->
                    <form id = "editTaskForm">
                        <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
                        <input type="hidden" name="projectowner_id" value="<?php echo $project_owner_id; ?>">
                        <input type="hidden" id="editTaskId" name="task_id">
                        <input class="input-style" type="text" id="editTaskName" name="task_name" placeholder="Task name">
                        <span id="editTaskName-error" class="error-message"></span>
                        <br>
                        <div class="div-space-top"></div>
                        <?php
                        global $connection;
                        // Fetch the users of the specified project using a prepared statement
                        $sql = "SELECT Users.user_id, Users.username FROM Users
                        JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
                        WHERE ProjectUsers.project_id = ?";


                        $stmt = mysqli_prepare($connection, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $projectId);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if ($result) {
                            echo '<select name="assignee" id="editAssignee" class="select-style">';

                            while ($row = mysqli_fetch_assoc($result)) {
                                $user_id = $row['user_id'];
                                $username = $row['username'];

                                // Generate the option tag
                                echo '<option value="' . $user_id . '">' . $username . '</option>';
                            }
                            echo '</select>';
                        } else {
                            // Handle the error in a more controlled way
                            echo 'Error fetching users: ' . mysqli_error($connection);
                        }
                        ?>
                        <span id="editAssignee-error" class="error-message"></span>
                        <div class="div-space-top"></div>
                        <div class="textarea-style">
                            <textarea id="editTaskDescription" name="task_description" placeholder="Task Description"></textarea>
                        </div>
                        <span id="editTaskDescription-error" class="error-message"></span>

                        <div class="div-space-top"></div>
                        <input class="input-style" type="text" id="editStartDate" name="start_date" onfocus="this.type='date'"
                            onblur="if(!this.value)this.type='text';" placeholder="Start Date">
                        <span id="editStartDate-error" class="error-message"></span>
                        <br>

                        <div class="div-space-top"></div>
                        <input class="input-style" type="text" id="editEndDate" name="end_date" onfocus="(this.type='date')"
                            onblur="if(!this.value)this.type='text';" placeholder="End Date">
                        <span id="editEndDate-error" class="error-message"></span>
                        <br>

                        <div class="div-space-top"></div>
                        <select id="editStatus" name="status" class="select-style">
                            <option value="" selected disabled hidden>Select status</option>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function () {
        initializeDateRangePicker('#editStartDate', '#editEndDate');
        fetchTaskDetails(<?php echo $task_id; ?>);
    });

    // Submit the edited task details when the form is submitted
    $('#editTaskForm').submit(function (event) {
        event.preventDefault();

        // Perform validation before submitting
        if (!updateFormValidation()) {
            return; // Stop form submission if validation fails
        }

        // Get the form data
        const formData = $(this).serialize();

        // Send an AJAX request to update the task details
        $.ajax({
            url: 'partial/task_partial/update_task.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status == 'success') {
                    displayPopupMessage(response.message, 'success');
                } else if (response.status === 'error') {
                    displayPopupMessage(response.message, 'error');
                }

            },
            error: function (xhr, status, error) {
                // Handle the error if needed
                console.error('Error updating task:', error);
                console.log(xhr.responseText);
            }
        });

    });

    function updateFormValidation() {
        // Clear previous error messages
        $('.error-message').text('');

        // Perform validation for each input field
        const taskName = $('#editTaskName').val();
        const assignee = $('#editAssignee').val();
        const taskDescription = $('#editTaskDescription').val();
        const startDate = $('#editStartDate').val();
        const endDate = $('#editEndDate').val();
        const status = $('#editStatus').val();
        const priority = $('#editPriority').val();

        // Add your validation rules here
        if (taskName.trim() === '') {
            $("#editTaskName-error").text("Task name is required.");
            return false;
        }

        if (assignee === null || assignee == 0 || assignee == -1) {
            $("#editAssignee-error").text("Task assignee is required.");
            return false;
        }


        if (taskDescription.trim() === '') {
            $("#editTaskDescription-error").text("Task description is required.");
            return false;
        }

        if (startDate === '') {
            $("#editStartDate-error").text("Task start date is required.");
            return false;
        }

        if (endDate === '') {
            $("#editEndDate-error").text("Task end date is required.");
            return false;
        }

        if (status === null || status == 0) {
            $("#editStatus-error").text("Task status is required.");
            return false;
        }

        if (priority === null || priority == 0) {
            $("#editPriority-error").text("Task priority is required.");
            return false;
        }

        return true; // All validation passed
    }


    // Function to fetch task details using AJAX
    function fetchTaskDetails(taskId) {
        // Clear previous error messages
        $('.error-message').text('');

        $.ajax({
            url: 'partial/task_partial/fetch_task_details.php', // Replace with the URL to your fetch task details PHP file
            method: 'GET',
            data: { task_id: taskId },
            success: function (response) {
                // Handle the response and populate the edit form here
                const taskDetails = JSON.parse(response);
                populateEditForm(taskDetails);
            },
            error: function (xhr, status, error) {
                // Handle the error if needed
                console.error('Error fetching task details:', error);
            }
        });
    }

    // Function to populate the edit form with task details
    function populateEditForm(taskDetails) {
        console.log(taskDetails);
        $('#editTaskId').val(taskDetails.task_id);
        $('#editTaskName').val(taskDetails.task_name);
        $('#editTaskDescription').val(taskDetails.task_description);

        // // Set the assignee select option
        var assigneeSelect = $('#editAssignee');

        // Check if the "Select Assignee" option already exists
        if (assigneeSelect.find('option[value="0"]').length === 0) {
            // If it doesn't exist, add "Select Assignee" option
            assigneeSelect.append($('<option>', {
                value: '0',
                text: 'Select Assignee',
                hidden: "hidden"
            }));
        }

        // Set the selected option based on response
        if (taskDetails['assignee'] == null) {
            // If status is null, select "Select Status"
            $('#editAssignee').val('0');
        }


        // $('#editAssignee').val(taskDetails.assignee);
        $('#editStartDate').val(taskDetails.start_date);
        $('#editEndDate').val(taskDetails.end_date);

        // Set the status select option
        var statusSelect = $('#editStatus');

        // Check if the "Select Status" option already exists
        if (statusSelect.find('option[value="0"]').length === 0) {
            // If it doesn't exist, add "Select Status" option
            statusSelect.append($('<option>', {
                value: '0',
                text: 'Select Status',
                hidden: "hidden"
            }));
        }

        // Set the selected option based on response
        if (taskDetails['status'] == null || taskDetails['status'] == 'New') {
            // If status is null, select "Select Status"
            $('#editStatus').val('0');
        } else {
            $('#editStatus').val(taskDetails.status);
        }

        // Set the priority select option
        var prioritySelect = $('#editPriority');

        // Check if the "Select priority" option already exists
        if (prioritySelect.find('option[value="0"]').length === 0) {
            // If it doesn't exist, add "Select priority" option
            prioritySelect.append($('<option>', {
                value: '0',
                text: 'Select Priority',
                hidden: "hidden"
            }));
        }

        // Set the selected option based on response
        if (taskDetails['priority'] == null) {
            // If priority is null, select "Select priority"
            $('#editPriority').val('0');
        } else {
            $('#editPriority').val(taskDetails.priority);
        }

    }

</script>