<?php
session_start();
?>

<?php include 'partial/navbar.php'; ?>

<?php
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<style>
    .members-list {
        display: flex;
        flex-wrap: wrap;
    }

    .member {
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .member span {
        color: var(--color-text);
    }

    .userrole-popup-container {
        color: var(--color-text);
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 300px;
        z-index: 1000;
    }

    .userole-close-popup {
        position: absolute;
        top: -5;
        right: 110;
        font-size: 20px;
        color: var(--color-text);
        cursor: pointer;
        z-index: 999;
    }

    .userole-close-popup:hover {
        color: var(--color-text-weak);
    }

    .member #popup-btn {
        margin-left: 0;
        margin-right: 0;
    }

    #updateRoleButton {
        display: none;
    }

    .update_userrole .button-style {
        margin-left: 5px;
        padding: 0 1px;
    }

    .member .user-role {
        margin-top: -10px;
        padding: 0;
        font-size: 12px;
        color: var(--color-text-weak);
    }

    .userrole-popup-container .error-message {
        font-size: 13px;
        font-family: inherit;
    }

    .project-dropdown {
        margin-left: 10px;
        position: relative;
        font-size: 16px;
        font-family: inherit;
    }
    
    .project-dropdown-menu {
        display: none;
        position: absolute;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--color-border);
        border-radius: 5px;
        top: 100%;
        left: 0;
        padding: 5px 5px;
        z-index: 1;
        background-color: var(--sidebar-bgcolor);
    }

    li.project-dropdown-menu-item p {
        margin-left: 10px;
        align-items: center;
    }

    .project-dropdown-menu-item:hover {
        background-color: var(--color-background-weak);
        border-radius: 5px;
    }

    .project_menu_toggle,
    .project-dropdown-menu {
        width: 120px;
    }

    .is-active .project-dropdown-menu {
        display: block;
    }
</style>
<title>Project - TeamTrack</title>
<div class="container project-wrapper">
    <?php include 'partial/sidebar.php'; ?>
    <?php

    // Check if the 'project_id' parameter is present in the URL
    if (isset($_GET['project_id'])) {
        $project_id = $_GET['project_id'];
        $user_id = $_SESSION['user_id'];

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
        <div class='heading-content'>
            <div class='heading-style'>
                <div class='project-link'>
                    <?php
                    if (mysqli_num_rows($result_project) > 0) {
                        $project = mysqli_fetch_assoc($result_project);
                        echo '<div class="square" style="background-color:' . $project['background_color'] . '"></div>';
                        echo "<p>" . $project['project_name'] . "</p>";
                    }
                    ?>
                    <div class="project-dropdown">
                        <div class="project_menu_toggle svg-img">
                            <img src="static/image/arrow-down.svg" alt="">
                        </div>
                        <ul class="project-dropdown-menu">
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

                            echo $project_id;
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
                            } else {
                                echo "<p class='user-role'>+ Add role</p>";
                            }

                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                        ?>

                        <div class="userrole-popup-container" id="userRolePopup">
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

                                <form>
                                    <div class="form-group">
                                        <p>Invite with email</p>
                                        <input type="email" id="email" name="email"
                                            placeholder="Add members by email...">
                                    </div>
                                    <div class="form-group">
                                        <label for="message">Message (optional)</label>
                                        <textarea id="message" name="message" rows="4"
                                            placeholder="Add a message"></textarea>
                                    </div>
                                    <button type="submit" class="invite-submit-btn">Send Invite</button>
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
                                <label for="taskname">Task Name</label>
                                <input type="text" name="taskname" id="taskname">
                                <span id="taskname-error" class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="task_description">Description</label>
                                <textarea type="text" name="task_description" id="task_description"></textarea>
                                <span id="task_description-error" class="error-message"></span>
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
                                    <table class="sortable" data-section="<?php echo $section; ?>">
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
                            <input type="hidden" id="editTaskId" name="task_id">
                            <label for="editTaskName">Task Name:</label>
                            <input type="text" id="editTaskName" name="task_name">
                            <br>

                            <label for="editAssignee">Assignee:</label>
                            <textarea id="editAssignee" name="assignee"></textarea>
                            <br>

                            <label for="editTaskDescription">Task Description:</label>
                            <textarea id="editTaskDescription" name="task_description"></textarea>
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
<!-- <script>
    // Add a single event listener to the parent element (.user-role-container)
    document.querySelector(".user-role-container").addEventListener("click", function (event) {
        var clickedElement = event.target.closest(".user-content");
        if (!clickedElement) {
            return; // Clicked outside .user-content, do nothing
        }

        // Retrieve the user_id and username from the clicked user-content element
        var userId = clickedElement.getAttribute("data-user-id");
        var username = clickedElement.querySelector(".user-name").textContent;

        // Use the user_id to set the value of the user_id input in the form for both remove and update user role forms
        var removeUserForm = document.querySelector(".remove-user-from-proj");
        var update_userrole = document.querySelector(".update_userrole");
        var userIdInput = removeUserForm.querySelector("input[name='user_id']");
        var userIdInput1 = update_userrole.querySelector("input[name='user_id']");
        userIdInput.value = userId;
        userIdInput1.value = userId;

        // Use the username to update the placeholder text for the user-role input in the form
        var userRoleInput = update_userrole.querySelector("input[name='user-role']");
        var userRoleValue = clickedElement.getAttribute("data-user-role");
        console.log(userRoleValue);

        if (userRoleValue !== null && userRoleValue !== "") {
            userRoleInput.value = userRoleValue;
        } else {
            userRoleInput.placeholder = "Specify " + username + "'s role in this project";
        }

        // Stop the click event from propagating to the document body
        event.stopPropagation();

        var popup = document.getElementById("userrole_popup");
        var userContentPosition = clickedElement.getBoundingClientRect();

        // Set the popup position based on the clicked user-content element
        popup.style.left = userContentPosition.left + 20 + "px";
        popup.style.top = userContentPosition.bottom + 12 + "px";

        if (popup.style.display === "block") {
            userrole_hidePopup();
        } else {
            userrole_showPopup();
        }
    });

    // Function to show the popup menu
    function userrole_showPopup() {
        var popup = document.getElementById("userrole_popup");
        popup.style.display = "block";
    }

    // Function to hide the popup menu
    function userrole_hidePopup() {
        var popup = document.getElementById("userrole_popup");
        popup.style.display = "none";
    }
</script> -->

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
            update: function (event, ui) {
                // Get the dragged task's ID
                const taskId = ui.item.attr('data-task-id');
                // Get the destination section's ID
                const sectionId = ui.item.closest('.collapsible').find('h2').text().trim();
                // Update the task's section in the database using an AJAX request
                $.ajax({
                    url: 'partial/task_partial/update_task_section.php', // Replace with the URL to your update task section PHP file
                    method: 'POST',
                    data: {
                        task_id: taskId,
                        section: sectionId
                    },
                    success: function (response) {
                        // Handle the response if needed
                        console.log('Task section updated successfully.');
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

    // Call the fetchTasks function on page load
    $(document).ready(function () {
        fetchTasks();
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

            // Get the form data
            const formData = $(this).serialize();

            // Send an AJAX request to update the task details
            $.ajax({
                url: 'partial/task_partial/update_task.php', // Replace with the URL to your update task PHP file
                method: 'POST',
                data: formData,
                success: function (response) {
                    // Handle the response if needed
                    console.log('Task updated successfully.');
                    // Hide the edit popup with animation
                    $('#taskPopup').removeClass('active');
                },
                error: function (xhr, status, error) {
                    // Handle the error if needed
                    console.error('Error updating task:', error);
                }
            });
        });


        // Close the popup when the close button is clicked
        $('#closeButton').click(function () {
            // Hide the popup with animation
            $('#taskPopup').removeClass('active');
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
                    task_id: taskId
                },
                success: function (response) {
                    // Handle the response if needed
                    console.log('Task deleted successfully.');
                    // Hide the popup with animation
                    $('#taskPopup').removeClass('active');
                    // Fetch tasks again to update the list
                    fetchTasks();
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
                        const assigneeName = task.assignee_name || 'Not Assigned';
                        const statusClass = task.status === 'Done' ? 'completed' : 'incomplete';
                        const row = `
                                <tr data-task-id="${task.task_id}" class="${statusClass}">
                                    <td>${task.task_name}</td>
                                    <td>${task.task_description}</td>
                                    <td>${assigneeName}</td>
                                    <td>${task.start_date}</td>
                                    <td>${task.end_date}</td>
                                    <td>${task.status}</td>
                                    <td>${task.priority}</td>
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
        $.ajax({
            url: 'partial/task_partial/fetch_task_details.php', // Replace with the URL to your fetch task details PHP file
            method: 'GET',
            data: { task_id: taskId },
            dataType: 'json',
            success: function (response) {
                // Populate the form fields with the fetched task details
                $('#editTaskId').val(response.task_id);
                $('#editTaskName').val(response.task_name);
                $('#editTaskDescription').val(response.task_description);
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
</script>
<script>
    function task_validateForm() {
        // Get form input values
        var taskname = document.getElementById('taskname').value;

        // Reset previous error messages
        document.getElementById('taskname-error').textContent = '';

        // Validate Task Name
        if (taskname === '') {
            $("#taskname-error").text("Task name is required.");
            return false;
        }

        $ajax({
            type: "POST",
            url: "partial/addtask.php",
            data: {
                taskname: taskname,
                task_description: task_description
            },
            dataType: "json",
            success: function (response) {
                if (response.status === "error") {
                    $("#taskname-error").text(response.message)
                }
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

                                        // userRoleInput.addEventListener('focus', function () {
                                        //     updateRoleButton.style.display = 'block';
                                        // });

                                        // userRoleInput.addEventListener('blur', function () {
                                        //     updateRoleButton.style.display = 'none';
                                        // });
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

        // Reset error message if there are no errors
        errorMessage.textContent = '';


        if (newRole === '') {
            errorMessage.textContent = 'Please select a valid role.'; // Display error message
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
                if (result === 'Success') {
                    console.log('User role updated successfully!');
                } else {
                    console.log('Error updating user role.');
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

        const onMouseUp = e => {
            if (!$menu.is(e.target) && $menu.has(e.target).length === 0) {
                $menu.removeClass('is-active');
            }
        };

        $('.project_menu_toggle').on('click', () => {
            $menu.toggleClass('is-active').promise().done(() => {
                if ($menu.hasClass('is-active')) {
                    $(document).on('mouseup', onMouseUp);
                } else {
                    $(document).off('mouseup', onMouseUp);
                }
            });
        });
    });
</script>