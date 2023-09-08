<?php include 'access_denied.php'; ?>

<?php
session_start();
?>

<?php include 'partial/navbar.php'; ?>

<?php
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<title>Project - TeamTrack</title>
<div class="container project-wrapper">
    <?php include 'partial/sidebar.php'; ?>
    <?php

    // Check if the 'project_id' parameter is present in the URL
    if (isset($_GET['project_id'])) {
        $project_id = $_GET['project_id'];
        $user_id = $_SESSION['user_id'];

        include 'partial/validation_check/check_user_exists_inproject.php';
        $userAssociated = check_user_exists_inproject($project_id);

        if (isset($_GET['invite']) && isset($_GET['verify'])) {


            if ($_GET['verify'] == 'false' && !$userAssociated && $userAssociated == 0 && $_GET['invite'] == 'true') { ?>
                <script>
                    $(document).ready(function () {
                        openOtpPopup(); // Opens the OTP verification popup$user_id = $_SESSION['user_id'];
                    });
                </script>

                <?php
            }
        } elseif (!$userAssociated && $userAssociated == 0) {
            echo '<h1 class="warning-style">Project not located or access is restricted.</h1>';
            exit();
        }

        // Query to fetch project details from the 'Projects' table
        $sql_project = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result_project = mysqli_query($connection, $sql_project);

        // Query to fetch project owner's details from the 'Users' table
        $sql_owner = "SELECT Users.*, ProjectUsers.is_projectowner, ProjectUsers.user_role FROM Users INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id WHERE ProjectUsers.project_id = $project_id AND ProjectUsers.is_projectowner = 1";
        $result_owner = mysqli_query($connection, $sql_owner);
        $project_owner = mysqli_fetch_assoc($result_owner);

        // Query to fetch other users associated with the project from the 'ProjectUsers' table (excluding the owner)
        $sql_users = "SELECT Users.user_id, Users.username, ProjectUsers.is_projectowner, ProjectUsers.user_role
        FROM Users
        JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
        WHERE ProjectUsers.project_id = $project_id
        ORDER BY ProjectUsers.is_projectowner DESC, Users.username";
        $result_users = mysqli_query($connection, $sql_users);

        // Query to get the total number of tasks for the project
        $sql_total_tasks = "SELECT COUNT(*) AS total_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id)";
        $result_total_tasks = mysqli_query($connection, $sql_total_tasks);
        $row_total_tasks = mysqli_fetch_assoc($result_total_tasks);
        $total_tasks = $row_total_tasks['total_tasks'];

        // Query to get the number of completed tasks for the project
        $sql_completed_tasks = "SELECT COUNT(*) AS completed_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id) AND status = 'completed'";
        $result_completed_tasks = mysqli_query($connection, $sql_completed_tasks);
        $row_completed_tasks = mysqli_fetch_assoc($result_completed_tasks);
        $completed_tasks = $row_completed_tasks['completed_tasks'];

        // Query to get the number of incomplete tasks for the project
        $sql_incomplete_tasks = "SELECT COUNT(*) AS incomplete_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id) AND status != 'completed'";
        $result_incomplete_tasks = mysqli_query($connection, $sql_incomplete_tasks);
        $row_incomplete_tasks = mysqli_fetch_assoc($result_incomplete_tasks);
        $incomplete_tasks = $row_incomplete_tasks['incomplete_tasks'];

        // Query to get the number of overdue tasks for the project
        $current_date = date('Y-m-d');
        $sql_overdue_tasks = "SELECT COUNT(*) AS overdue_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id) AND status != 'completed' AND end_date < '$current_date'";
        $result_overdue_tasks = mysqli_query($connection, $sql_overdue_tasks);
        $row_overdue_tasks = mysqli_fetch_assoc($result_overdue_tasks);
        $overdue_tasks = $row_overdue_tasks['overdue_tasks'];


    } else {
        echo "Project not found.";
    }
    ?>

    <div class='main-content'>
        <div class='heading-content sticky-heading'>
            <div class='heading-style'>
                <div class='project-link project-wrapper project_menu_toggle'>
                    <?php
                    if (mysqli_num_rows($result_project) > 0) {
                        $project = mysqli_fetch_assoc($result_project);
                        echo '<div class="square project-wrapper" style="background-color:' . $project['background_color'] . '"><img src="static/image/project.svg" alt="Image" class="overlay-image"></div>';
                        echo "<p class='project-name'>" . capitalizeEachWord($project['project_name']) . "</p>";
                    }
                    ?>
                    <div class="project-dropdown">
                        <div class="svg-img">
                            <img src="static/image/arrow-down.svg" alt="">
                        </div>
                        <ul class="project-dropdown-menu popup-style">
                            <li class="project-dropdown-menu-item indicate-danger">
                                <?php
                                echo '<a href="partial/project_partial/leave_project.php?project_id=' . $project_id . '">';
                                ?>
                                <p>Leave Project</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="otpPopup" class="otp-popup popup-style" style="display:none;">
                <div id="otpPopupContent" class="otp-popup-content">
                    <p class="heading-style">OTP Verification '
                        <?php echo $project['project_name'] ?>'
                    </p>
                    <div class="bottom-line"></div>
                    <div class="div-space-top"></div>
                    <form id="otpForm">
                        <input type="text" class="input-style" id="otpInput" name="otpInput" placeholder="Enter OTP">

                        <button type="button" class="button-style" id="verifyOtpButton">Verify OTP</button>
                    </form>
                    <p id="otpStatus"></p>
                </div>
            </div>
            <div class="tab-btns">
                <!-- Tab Buttons -->
                <div class="heading-nav between-verticle-line tab-btn active" onclick="openTab(event, 'tab1')">Overview
                </div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab2')">List</div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab3')">Dashboard</div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab4')">Messages</div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab5')">Files</div>
            </div>
        </div>
        <div class="bottom-line"></div>

        <div class="tab-content div-space-top active" id="tab1">
            <div class="overview-section">
                <div class="heading-style">
                    <p>Project description</p>
                </div>

                <div class="project-desc-textarea textarea-style">
                    <textarea name="" id="" cols="50" rows="6" placeholder="What's this project about?"><?php
                    // Check if the description is not null and not an empty string before echoing
                    if ($project['description'] !== null && $project['description'] !== "") {
                        echo $project['description'];
                    }
                    ?></textarea>
                    <div class="div-space-top"></div>

                </div>
                <div class="project-role-container">
                    <div class="heading-style">
                        <p>Project roles</p>
                    </div>
                    <div class='user-role-container'>
                        <div class='user-content add-member' onclick='addmember_popup_toggle()'>
                            <p><span class='border-around-plus'>+</span> Add member</p>
                        </div>
                        <?php
                        echo '<div class="members-list">';
                        while ($row = mysqli_fetch_assoc($result_users)) {
                            $userId = $row['user_id'];
                            $username = $row['username'];
                            $isProjectOwner = $row['is_projectowner'];
                            $userRole = $row['user_role'];

                            echo '<div class="member user-content" data-project-id="' . $project_id . '">';
                            display_profile_picture($userId);
                            echo '<div class="user-role-flex">';
                            echo '<span class="username" data-user-id="' . $userId . '">' . $username . '</span>';
                            if ($isProjectOwner) {
                                echo '';
                            }
                            if ($userRole) {
                                $user_role = $userRole;
                                echo "<p class='user-role'>$user_role</p>";
                            } elseif ($isProjectOwner) {
                                echo "<p class='user-role'>Project Owner</p>";
                            } elseif ($project_owner['user_id'] == $user_id) {
                                echo "<p class='user-role'>+ Add role</p>";
                            }

                            echo '</div>';
                            // }
                            echo '</div>';
                        }
                        echo '</div>';
                        ?>

                        <div class="userrole-popup-container popup-style" id="userRolePopup">
                            <span class="userole-close-popup" id="closeUserRolePopup">&times;</span>
                            <div class="userrole-popup-content">
                                <ul>
                                    <li>
                                        <form id="userRoleForm" method="post" class="update_userrole">
                                            <div id="error-message" class="error-message"></div>
                                            <div class="input-container">
                                                <input type="hidden" name="project_id"
                                                    value="<?php echo $project_id; ?>">
                                                <input type="hidden" name="user_id" value="">
                                                <input type="text" name="user-role" class="input-style"
                                                    id="userRoleInput" placeholder="Enter role">
                                                <!-- <button type="submit" name="update_userrole" class="button-style"
                                                    id="updateRoleButton">Update</button> -->
                                            </div>
                                        </form>
                                    <li>
                                        <form id="removeUserForm" class="remove-user-from-proj">
                                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                            <input type="hidden" name="user_id" value="">
                                            <button type="submit" class="indicate-danger" name="remove_user">Remove
                                                User</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="invite-popup" id="invite-popup">
                            <div class="invite-popup-content">
                                <span class="invite-popup-close" onclick="addmember_popup_toggle()">&times;</span>
                                <p class="heading-style">Share '
                                    <?php echo $project['project_name'] ?>'
                                </p>
                                <div class="bottom-line"></div>
                                <div class="div-space-top"></div>
                                <form id="invite-form">

                                    <p>Invite with email</p>
                                    <input type="email" class="input-style" id="email" name="email"
                                        placeholder="Add members by email...">
                                    <p id="email-exists-message" class="indicate-danger"></p>

                                    <div class="form-group">
                                        <label for="message">Message <p
                                                style="display:inline; color: var(--color-text-weak);">(optional)</p>
                                        </label>
                                        <div class="textarea-style">
                                            <textarea id="message" name="message" rows="4"
                                                placeholder="Add a message"></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="button-style">Send Invite</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content div-space-top" id="tab2">
            <div class="tasks-section">
                <button class="addtask-btn" onclick="addtask_popup_toggle()">+ Add Task</button>

                <div class="addtask-popup" id="addtask-popup">
                    <div class="addtask-popup-content">
                        <form action="partial/addtask.php" method="post" enctype="multipart/form-data"
                            onsubmit="return task_validateForm()">
                            <span class="addtask-popup-close" onclick="addtask_popup_toggle()">&times;</span>
                            <p class="heading-style">Add Task</p>
                            <div class="bottom-line"></div>
                            <div class="div-space-top"></div>
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <div class="form-group">
                                <span id="taskname-error" class="error-message"></span>
                                <input class="input-style" type="text" name="taskname" id="taskname"
                                    placeholder="Enter Task Name">
                            </div>
                            <div class="form-group">
                                <label for="task_description">Description</label>
                                <div class="textarea-style">
                                    <textarea type="text" name="task_description" id="task_description"></textarea>
                                    <span id="task_description-error" class="error-message"></span>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn-style">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="lst-of-tasks div-space-top">
                    <?php
                    // Check if the 'project_id' parameter is present in the URL
                    if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
                        $project_id = $_GET['project_id'];

                        // Prepare and execute the SQL query using a prepared statement
                        $stmt = $connection->prepare("SELECT * FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = ?)");
                        $stmt->bind_param("i", $project_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Store tasks grouped by section
                        $tasksBySection = array(
                            "To Do" => array(),
                            "Doing" => array(),
                            "Done" => array()
                        );

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $section = $row['section'];
                                if (!isset($tasksBySection[$section])) {
                                    $tasksBySection[$section] = array();
                                }
                                $tasksBySection[$section][] = $row;
                            }
                        }
                        $stmt->close();
                    }

                    // Function to update the task's status based on the completion status
                    function getTaskStatusClass($status)
                    {
                        if ($status === 'Done') {
                            return 'completed';
                        } else {
                            return 'incomplete';
                        }
                    }
                    ?>
                    <?php if (isset($tasksBySection)): ?>
                        <?php if (!empty($tasksBySection)): ?>
                            <?php foreach ($tasksBySection as $section => $tasks): ?>
                                <div class="collapsible">
                                    <h2>
                                        <?php echo $section; ?>
                                    </h2>
                                    <table class="sortable show" data-section="<?php echo $section; ?>">
                                        <thead>
                                            <tr>
                                                <th>Task Name</th>
                                                <th>Task Description</th>
                                                <th>Assignee</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tasks as $task): ?>
                                                <tr data-task-id="<?php echo $task['task_id']; ?>"
                                                    class="<?php echo getTaskStatusClass($task['status']); ?>">
                                                    <td>
                                                        <?php echo $task['task_name']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $task['task_description']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $task['assignee']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $task['start_date']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $task['end_date']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $task['status']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $task['priority']; ?>
                                                    </td>
                                                    <!-- <td>
                                        <button class="delete-btn" data-task-id="<?php echo $task['task_id']; ?>">Delete</button>
                                    </td> -->
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No tasks assigned to this project.</p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Slide-in popup to display task description -->
                    <div class="task-popup" id="taskPopup">
                        <div class="heading-content">
                            <div class="heading-style">
                                <p>Edit Task</p>
                            </div>
                            <div class="bottom-line"></div>
                            <div class="div-space-top"></div>
                            <button type="button" id="closeButton">Close</button>
                            <button type="button" id="deleteButton">Delete Task</button>
                            <div class="div-space-top"></div>
                        </div>
                        <div class="bottom-line"></div>
                        <div class="div-space-top"></div>
                        <form id="editTaskForm">
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <input type="hidden" name="projectowner_id"
                                value="<?php echo $project_owner['user_id']; ?>">
                            <input type="hidden" id="editTaskId" name="task_id">
                            <input class="input-style" type="text" id="editTaskName" name="task_name">
                            <span id="editTaskName-error" class="error-message"></span>
                            <br>

                            <div class="div-space-top"></div>


                            <?php include 'partial/project_partial/populate_assignee.php';
                            populateAssigneeOptions($project_id);
                            ?>





                            <span id="editAssignee-error" class="error-message"></span>


                            <div class="div-space-top"></div>
                            <div class="textarea-style">
                                <textarea id="editTaskDescription" name="task_description"></textarea>
                            </div>
                            <span id="editTaskDescription-error" class="error-message"></span>

                            <div class="div-space-top"></div>
                            <input class="input-style" type="text" id="editStartDate" name="start_date"
                                placeholder="Start Date" onfocus="(this.type='date')">
                            <span id="editStartDate-error" class="error-message"></span>
                            <br>

                            <div class="div-space-top"></div>
                            <input class="input-style" type="text" id="editEndDate" name="end_date"
                                placeholder="End Date" onfocus="(this.type='date')">
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
            </div>
        </div>
        <div class="tab-content div-space-top" id="tab3">
            <div class="dashboard-section">
                <div class="dashboard-item complete">
                    <div class="complete-title">
                        <p class="heading">Complete Tasks</p>
                    </div>
                    <div class="count">
                        <span>
                            <?php echo $completed_tasks ?>
                        </span>
                    </div>
                </div>
                <div class="dashboard-item incomplete">
                    <div class="incomplete-title">
                        <p class="heading">Incomplete Tasks</p>
                    </div>
                    <div class="count">
                        <span>
                            <?php echo $incomplete_tasks ?>
                        </span>
                    </div>
                </div>
                <div class="dashboard-item overdue-tasks">
                    <div class="overdue-title">
                        <p class="heading">Overdue Tasks</p>
                    </div>
                    <div class="count">
                        <span>
                            <?php echo $overdue_tasks ?>
                        </span>
                    </div>

                </div>
                <div class="dashboard-item total-tasks">
                    <div class="total-title">
                        <p class="heading">Total Tasks</p>
                    </div>
                    <div class="count">
                        <span>
                            <?php echo $total_tasks ?>
                        </span>
                    </div>

                </div>
            </div>
        </div>
        <div class="tab-content div-space-top" id="tab4">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div>
        <div class="tab-content div-space-top" id="tab5">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div>

        <!-- Include jQuery library -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </div>

</div>
<script>
    function addmember_popup_toggle() {
        const popup = document.getElementById('invite-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }
</script>
<script>
    function addtask_popup_toggle() {
        const popup = document.getElementById('addtask-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    // Call the fetchTasks function on page load
    $(document).ready(function () {
        fetchTasks();
    });

    // JavaScript for collapsible sections
    $(document).ready(function () {
        $('.collapsible h2').click(function () {
            $(this).toggleClass('active');
            $(this).next('table').toggleClass('show');
        });
    });

    // JavaScript for sorting between sections
    $(document).ready(function () {
        // Initialize sortable for each section table
        $('.sortable').sortable({
            connectWith: '.sortable', // Enable sorting between sections
            placeholder: 'ui-state-highlight', // Style for the placeholder during drag-and-drop
            items: 'tr', // Limit sorting to rows only within the current section
            scroll: true, // Enable scrolling
            scrollSensitivity: 50, // Adjust the value as needed
            update: function (event, ui) {
                // Get the dragged task's ID
                const taskId = ui.item.attr('data-task-id');

                // Get the destination section's ID
                const sectionId = ui.item.closest('.collapsible').find('h2').text().trim();
                console.log(sectionId);


                // Update the task's section in the database using an AJAX request
                $.ajax({
                    url: 'partial/task_partial/update_task_section.php', // Replace with the URL to your update task section PHP file
                    method: 'POST',
                    data: {
                        projectowner_id: <?php echo $project_owner['user_id']; ?>,
                        task_id: taskId,
                        section: sectionId
                    },
                    success: function (response) {
                        console.log(response);
                        // fetchTasks();
                        console.log('Task section updated successfully.');
                        if (response.status == 'success') {
                            console.log(response.message);
                            displayPopupMessage(response.message, 'success');
                        } else if (response.status === 'error') {
                            displayPopupMessage(response.message, 'error');
                        }

                        fetchTasks();
                    },
                    error: function (xhr, status, error) {
                        // Handle the error if needed
                        console.error('Error updating task section:', error);
                    }
                });

            }
        });
    });

    // Variable to store the current task ID
    let currentTaskId;

    // JavaScript for handling task click and displaying popup
    $(document).ready(function () {
        // Check if the clicked element is not the "Save Changes" button
        function isNotSaveButton(target) {
            return !$(target).closest('#submitButton').length;
        }

        // Use event delegation for the click event on task rows
        $(document).on('click', '.sortable tr', function (event) {
            // Check if the clicked element is not the "Save Changes" button
            if (isNotSaveButton(event.target)) {
                // Get the task details from the clicked row
                const taskId = $(this).attr('data-task-id');
                const taskName = $(this).find('td:nth-child(1)').text();
                const taskDescription = $(this).find('td:nth-child(2)').text();
                const assingee = $(this).find('td:nth-child(3)').text();
                const startDate = $(this).find('td:nth-child(4)').text();
                const endDate = $(this).find('td:nth-child(5)').text();
                const status = $(this).find('td:nth-child(6)').text();
                const priority = $(this).find('td:nth-child(7)').text();

                // Remove the active class from all task rows
                $('.sortable tr').removeClass('active-task');

                // Add the active class to the clicked task row
                $(this).addClass('active-task');

                console.log(taskId);

                // Set the task details in the edit popup form
                $('#editTaskId').val(taskId);
                $('#editTaskName').val(taskName);
                $('#editTaskDescription').val(taskDescription);
                $('#editAssignee').val(assingee);
                $('#editStartDate').val(startDate);
                $('#editEndDate').val(endDate);
                $('#editStatus').val(status);
                $('#editPriority').val(priority);

                // Store the task ID in the variable
                currentTaskId = taskId;

                // Show the popup with animation
                $('#taskPopup').addClass('active');


                // Fetch task details and populate the edit form
                fetchTaskDetails(taskId);
            }
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
            console.log(formData);

            // Send an AJAX request to update the task details
            $.ajax({
                url: 'partial/task_partial/update_task.php', // Replace with the URL to your update task PHP file
                method: 'POST',
                data: formData,
                success: function (response) {

                    // Handle the response if needed
                    console.log('Task updated successfully.');

                    if (response.status == 'success') {
                        console.log(response.message);
                        displayPopupMessage(response.message, 'success');
                    } else if (response.status === 'error') {
                        displayPopupMessage(response.message, 'error');
                    }

                    // Hide the edit popup with animation
                    $('#taskPopup').removeClass('active');

                    fetchTasks();

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

            if (assignee === null) {

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

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const selectedStartDate = new Date(startDate);
            selectedStartDate.setHours(0, 0, 0, 0);

            if (selectedStartDate.getTime() <= today.getTime()) {
                $("#editStartDate-error").text("Start date cannot be in the past.");
                return false;
            }

            if (endDate === '') {
                $("#editEndDate-error").text("Task end date is required.");
                return false;
            }

            if (new Date(endDate) <= selectedStartDate) {
                $("#editEndDate-error").text("End date must be after start date.");
                return false;
            }

            if (status === null) {
                $("#editStatus-error").text("Task status is required.");
                return false;
            }

            if (priority === null) {
                $("#editPriority-error").text("Task priority is required.");
                return false;
            }

            return true; // All validation passed
        }


        // Close the popup when the close button is clicked
        $('#closeButton').click(function () {
            // Hide the popup with animation
            $('#taskPopup').removeClass('active');

            // Remove the active class from all task rows
            $('.sortable tr').removeClass('active-task');

        });

        // Delete the task when the delete button is clicked
        $('#deleteButton').click(function () {
            // Get the task ID from the variable
            const taskId = currentTaskId;

            // Send an AJAX request to delete the task
            $.ajax({
                url: 'partial/task_partial/delete_task.php', // Replace with the URL to your delete task PHP file
                method: 'POST',
                data: {
                    projectowner_id: <?php echo $project_owner['user_id']; ?>,
                task_id: taskId
            },
                success: function (response) {
                    // Handle the response if needed
                    console.log('Task deleted successfully.');
                    // Hide the popup with animation
                    $('#taskPopup').removeClass('active');
                    // Remove the active class from all task rows
                    $('.sortable tr').removeClass('active-task');

                    // Fetch tasks again to update the list
                    fetchTasks();
                    if (response.status == 'success') {
                        console.log(response.message);
                        displayPopupMessage(response.message, 'success');
                    } else if (response.status === 'error') {
                        displayPopupMessage(response.message, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle the error if needed
                    console.error('Error deleting task:', error);
                }
            });
    });
    });

    // Function to fetch tasks from the server using AJAX
    function fetchTasks() {
        // Get the project_id from the URL
        const project_id = <?php echo isset($_GET['project_id']) ? $_GET['project_id'] : 'null'; ?>;
        if (project_id === null) {
            console.error('No project_id found in URL.');
            return;
        }

        // Send an AJAX request to fetch tasks for the given project_id
        $.ajax({
            url: 'partial/task_partial/fetch_tasks.php',
            method: 'GET',
            data: { project_id: project_id },
            success: function (response) {
                // Clear the existing tasks
                $('.collapsible table tbody').empty();

                $.each(response, function (section, tasks) {
                    const tableBody = $('.collapsible table[data-section="' + section + '"] tbody');
                    $.each(tasks, function (index, task) {
                        const MAX_DESCRIPTION_LENGTH = 50; // Set the maximum length you want to display
                        const taskDescription = task.task_description.length > MAX_DESCRIPTION_LENGTH
                            ? task.task_description.substring(0, MAX_DESCRIPTION_LENGTH) + '...'
                            : task.task_description;
                        const assigneeName = task.assignee_name ? task.assignee_name : 'Not Assigned';
                        const start_date = task.start_date ? task.start_date : '-';
                        const end_date = task.end_date ? task.end_date : '-';
                        const status = task.status ? task.status : '-';
                        const priority = task.priority ? task.priority : '-';

                        const statusClass = task.status === 'Done' ? 'completed' : 'incomplete';
                        const row = `
                                <tr data-task-id="${task.task_id}" class="${statusClass}">
                                    <td>${task.task_name}</td>
                                    <td>${taskDescription}</td>
                                    <td>${assigneeName}</td>
                                    <td>${start_date}</td>
                                    <td>${end_date}</td>
                                    <td>${status}</td>
                                    <td>${priority}</td>
                                </tr>`;
                        tableBody.append(row);
                    });
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching tasks:', error);
            }
        });
    }

    // Function to fetch task details using AJAX
    function fetchTaskDetails(taskId) {
        // Clear previous error messages
        $('.error-message').text('');

        $.ajax({
            url: 'partial/task_partial/fetch_task_details.php', // Replace with the URL to your fetch task details PHP file
            method: 'GET',
            data: { task_id: taskId },
            dataType: 'json',
            success: function (response) {
                console.log("--")
                console.log(response);
                console.log("--")
                // Populate the form fields with the fetched task details
                $('#editTaskId').val(response.task_id);
                $('#editTaskName').val(response.task_name);
                $('#editTaskDescription').val(response.task_description);


                // Set the assignee select option
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

                // Check if the "User not found" option already exists
                if (assigneeSelect.find('option[value="-1"]').length === 0) {
                    // If it doesn't exist, add "Select Assignee" option
                    assigneeSelect.append($('<option>', {
                        value: '-1',
                        text: 'Assignee not found',
                        hidden: "hidden"
                    }));
                }

                // Set the selected option based on response
                if (response.assignee == null) {
                    // If assignee is null, select "Select Assignee"
                    $('#editAssignee').val('0');
                } else {
                    check_assignee_exists(taskId, response);
                }

                $('#editStartDate').val(response.start_date);
                $('#editEndDate').val(response.end_date);
                $('#editStatus').val(response.status);
                $('#editPriority').val(response.priority);
            },
            error: function (xhr, status, error) {
                // Handle the error if needed
                console.error('Error fetching task details:', error);
            }
        });
    }

    function check_assignee_exists(taskId, response) {
        $.ajax({
            url: 'partial/project_partial/check_assignee_exists_inproject.php', // Replace with the URL to your fetch task details PHP file
            method: 'GET',
            data: { project_id: <?php echo $project_id; ?>, task_id: taskId
        },
            dataType: 'json',
            success: function (exists_assignee_response) {
                console.log(exists_assignee_response.result);
                if (exists_assignee_response.result == true) {
                    // Assign the response.assignee value
                    $('#editAssignee').val(response.assignee);
                } else {
                    // If assignee is not found, select "Assignee not found"
                    $('#editAssignee').val('-1');
                }
            },
            error: function (xhr, status, error) {
                // Handle the error if needed
                console.error('Error fetching task details:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
            }
        });
    }
</script>
<script>
    function task_validateForm() {
        // Get form input values
        var taskname = document.getElementById('taskname').value;
        var taskdescription = document.getElementById('task_description').value;

        // Reset previous error messages
        document.getElementById('taskname-error').textContent = '';
        document.getElementById('task_description-error').textContent = '';

        // Validate Task Name
        if (taskname === '') {
            $("#taskname-error").text("Task name is required.");
            return false;
        }

        // Validate Task Description
        if (taskdescription === '') {
            $("#task_description-error").text("Task_description is required.");
            return false;
        }

        $.ajax({
            method: "POST",
            url: "partial/addtask.php",
            data: {
                project_id: <?php echo $project_id ?>,
                taskname: taskname,
                task_description: taskdescription
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response.status == 'success') {
                    console.log(response.message);
                    displayPopupMessage(response.message, 'success');
                } else if (response.status === 'error') {
                    $("#taskname-error").text(response.message)
                    displayPopupMessage(response.message, 'error');
                }

                // Reset form fields after successful submission
                document.getElementById('taskname').value = '';
                document.getElementById('task_description').value = '';

                // toggling the add task popup, if it is in block then change to none and vice verse.
                addtask_popup_toggle();

                // Fetching the task again.
                fetchTasks();
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });

        // Prevent the default form submission
        return false;
    }
</script>

<script>
    const members = document.querySelectorAll('.member');
    const userRoleForm = document.getElementById('userRoleForm');
    const userRoleInput = document.getElementById('userRoleInput');
    const updateRoleButton = document.getElementById('updateRoleButton');

    members.forEach(member => {
        member.addEventListener('click', function () {
            const errorMessage = document.getElementById('error-message'); // Get the error message element
            // Reset error message if there are no errors
            errorMessage.textContent = '';

            // Remove 'active' class from all usernames
            const usernames = document.querySelectorAll('.username');
            usernames.forEach(username => {
                username.classList.remove('active');
            });

            const username = this.querySelector('.username');
            username.classList.add('active');

            const userId = username.getAttribute('data-user-id');
            const project_owner_value = <?php echo $project_owner['user_id']; ?>;
            if (project_owner_value != userId) {
                console.log(project_owner_value != userId);

                // Make an AJAX request to check if the user is a project owner
                fetch('partial/project_partial/check_project_owner.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_owner) {
                            // Make an AJAX request to fetch user role
                            fetch('partial/project_partial/get_user_role.php', {
                                method: 'POST',
                                body: new URLSearchParams({ user_id: userId }),
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            })
                                .then(response => response.text())
                                .then(userRole => {
                                    if (userRole !== 'Error') {
                                        const userRoleInput = document.getElementById('userRoleInput');
                                        userRoleInput.value = `${userRole}`;

                                        const userRolePopup = document.getElementById('userRolePopup');

                                        // Position the popup below the clicked member
                                        const rect = member.getBoundingClientRect();
                                        userRolePopup.style.left = rect.left + 'px';
                                        userRolePopup.style.top = rect.bottom + 10 + 'px';

                                        userRolePopup.style.display = 'block';
                                    } else {
                                        alert('Error fetching user role.');
                                    }
                                })
                                .catch(error => {
                                    alert('An error occurred while fetching user role.');
                                });
                        } else {
                            console.log('You are not a project owner.');
                        }
                    })
                    .catch(error => {
                        alert('An error occurred while checking project ownership.');
                    });
            } else {
                console.log("bye");
            }
        });
    });

    document.getElementById('closeUserRolePopup').addEventListener('click', function () {
        closePopup();
    });

    document.addEventListener('click', function (event) {
        const popupContainer = document.getElementById('userRolePopup');
        if (!popupContainer.contains(event.target)) {
            closePopup();
        }
    });

    function closePopup() {
        const userRolePopup = document.getElementById('userRolePopup');
        userRolePopup.style.display = 'none';
    }

    // Add an event listener to the form submission
    document.getElementById('userRoleForm').addEventListener('submit', function (event) {
        event.preventDefault();

        // alert("test1");

        const userRoleInput = document.getElementById('userRoleInput');
        // alert(userRoleInput);
        const newRole = userRoleInput.value.trim(); // Trim whitespace

        const errorMessage = document.getElementById('error-message'); // Get the error message element
        console.log(errorMessage);

        // Reset error message if there are no errors
        errorMessage.textContent = '';


        if (newRole === '') {
            errorMessage.textContent = 'Please enter a valid role.'; // Display error message
            return;
        }

        const userId = document.querySelector('.username.active').getAttribute('data-user-id'); // Assuming you have a class 'active' for the selected member
        const projectId = document.querySelector('.member').getAttribute('data-project-id'); // Get the project ID from the clicked member

        errorMessage.textContent = '';
        // Make an AJAX request to update the user role
        fetch('partial/project_partial/update_userrole.php', {
            method: 'POST',
            body: new URLSearchParams({ project_id: projectId, user_id: userId, new_role: newRole }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
            .then(response => response.text())
            .then(result => {
                console.log(result.status);
                if (result.status == 'success') {
                    console.log(result.message);
                    displayPopupMessage(result.message, 'success');
                } else if (result.status === 'error') {
                    displayPopupMessage(result.message, 'error');
                }
            })
            .catch(error => {
                console.log('An error occurred while updating user role.');
            });
        location.reload(); // Reload the page after successful update
    });

</script>

<script>
    // Add an event listener to the form submission
    document.getElementById('removeUserForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const userId = document.querySelector('.username.active').getAttribute('data-user-id'); // Assuming you have a class 'active' for the selected member
        const projectId = document.querySelector('.member').getAttribute('data-project-id'); // Get the project ID from the clicked member

        fetch('partial/project_partial/remove_user_from_project.php', {
            method: 'POST',
            body: new URLSearchParams({ project_id: projectId, user_id: userId }),

        })
            .then(response => response.text())
            .then(result => {
                if (result === 'Success') {
                    console.log('User removed successfully!');
                    // You can perform any additional actions here after successful removal
                } else {
                    console.log('Error removing user.');
                }
            })
            .catch(error => {
                console.log('An error occurred while removing user.');
            });
        location.reload(); // Reload the page after successful update
    });
</script>

<script>
    $(document).ready(function () {
        const $menu = $('.project-dropdown');

        const onClick = e => {
            if ($menu.hasClass('is-active')) {
                if (!$menu.is(e.target) && $menu.has(e.target).length === 0) {
                    $menu.removeClass('is-active');
                    $(document).off('click', onClick);
                }
            }
        };

        $('.project_menu_toggle').on('click', () => {
            $menu.toggleClass('is-active').promise().done(() => {
                if ($menu.hasClass('is-active')) {
                    $(document).on('click', onClick);
                } else {
                    $(document).off('click', onClick);
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function () {
        var emailInput = $("#email");

        emailInput.keyup(function () {
            var email = emailInput.val();

            // Frontend validation
            if (email === "") {
                $("#email-exists-message").text("");
                return;
            }

            // AJAX request to check if the email exists in the project
            $.ajax({
                type: "POST",
                url: "partial/project_partial/is_exist_user.php", // Your existing email checking script
                data: { email: email, project_id: <?php echo $project_id ?> },
                dataType: "json",
                success: function (response) {
                    if (response.exists) {
                        $("#email-exists-message").text("A user with this email is already part of the project.");
                    } else {
                        $("#email-exists-message").text("");
                    }
                },
                error: function () {
                    alert("An error occurred while checking the email.");
                }
            });
        });

        $("#invite-form").submit(function (event) {
            event.preventDefault();

            var email = emailInput.val();
            var message = $("#message").val();

            console.log(email);
            console.log(message);

            // Frontend validation
            if (email === "") {
                alert("Please enter an email address.");
                return;
            }

            // Proceed with inviting the user to the project
            inviteUserToProject(email, message);

            // Clear input fields after successful submission
            $("#email").val("");
            $("#message").val("");
        });

        function inviteUserToProject(email, message) {
            // AJAX request to invite the user to the project
            $.ajax({
                type: "POST",
                url: "partial/project_partial/invite_to_project.php", // Backend processing script
                data: { email: email, message: message, project_id: <?php echo $project_id ?> },
                dataType: "json",
                success: function (response) {
                    console.log("hello");
                    console.log(response);
                    console.log("hello");
                    if (response.status == 'success') {
                        console.log(response.message);
                        displayPopupMessage(response.message, 'success');
                    } else if (response.status === 'error') {
                        displayPopupMessage(response.message, 'error');
                    }
                }
            });

            addmember_popup_toggle()
        }
    });

</script>
<script>
    function openOtpPopup() {
        $("#otpPopup").show();
    }

    function closeOtpPopup() {
        $("#otpPopup").hide();
    }

    $(document).ready(function () {
        $("#verifyOtpButton").click(function () {
            var enteredOtp = $("#otpInput").val();

            $.ajax({
                type: "POST",
                url: "partial/verify_addmember_otp.php",
                data: { otp: enteredOtp, project_id: "<?php echo $project_id ?>", },
                success: function (response) {
                    $("#otpStatus").html(response);
                }
            });

            closeOtpPopup()
            location.reload();

        });
    });
</script>