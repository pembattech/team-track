<!-- <?php include 'access_denied.php'; ?> -->

<title>Project - TeamTrack</title>
<style>
    .error-message {
        color: red;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        cursor: pointer;
    }

    .collapsible {
        border: 1px solid #ddd;
        margin-bottom: 10px;
    }

    .collapsible h2 {
        margin: 0;
        padding: 10px;
        cursor: pointer;
    }

    /* .collapsible h2.active {
        background-color: #ddd;
    } */

    .collapsible table {
        display: none;
    }

    .collapsible table.show {
        display: table;
    }

    /* Styles for the slide-in popup */
    .task-popup {
        position: fixed;
        top: 139;
        right: -440px;
        width: 400px;
        height: 100%;
        background-color: var(--sidebar-bgcolor);
        color: var(--color-text);
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        transition: right 0.3s ease-in-out;
    }

    #editTaskForm input[type="text"],
    #editTaskForm textarea,
    #editTaskForm input[type="date"],
    #editTaskForm select {
        border: 0;
        background-color: var(--color-background-weak);
    }

    button#submitButton,
    button#deleteButton,
    button#closeButton {
        outline: 0;
        padding: 3px;
        border: 1px solid var(--color-border);
        border-radius: 5px;
        color: var(--color-text-weak);
        background-color: var(--color-background-weak);
    }

    button#submitButton:hover,
    button#deleteButton:hover,
    button#closeButton:hover {
        background-color: var(--color-background-hover);
        color: var(--color-text);
    }

    /* Show the slide-in popup */
    .task-popup.active {
        right: 0;
    }
</style>
<?php include 'partial/navbar.php'; ?>

<style>
    .role_popup-menu {
        display: none;
        position: absolute;
        background-color: var(--sidebar-bgcolor);
        width: 250px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 9;
        border: 1px solid var(--color-border);
        border-radius: 5px;
    }

    /* .role_popup-menu a { */
    .role_popup-menu li {
        display: block;
        padding: 5px;
        font-size: 14px;
        font-family: inherit;
        text-decoration: none;
        color: var(--color-text);
    }

    .role_popup-menu .heading-style {
        font-size: 14px;
    }

    .role_popup-menu li:hover {
        border-radius: 5px;
        background-color: var(--color-background-weak);
    }

    .remove-user-from-proj button {
        appearance: none;
        outline: none;
        border: none;
        background-color: inherit;
        font-size: 14px;
    }

    .input-container {
        display: flex;
        align-items: center;
    }

    .input-container input[type="text"] {
        flex: 1;
        width: 100px;
    }

    .relative-button {
        background-color: #4CAF50;
        color: white;
        padding: 2px 4px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .sortable {
        border: 1px solid #ccc;
        padding: 10px;
    }
</style>
<div class="container project-wrapper">
    <?php include 'partial/sidebar.php'; ?>
    <?php

    session_start();

    function get_tasks_by_section($project_id, $section)
    {
        global $connection;
        $project_id = sanitize_input($project_id);
        $section = sanitize_input($section);

        $sql = "SELECT * FROM Tasks WHERE project_id = $project_id AND section = '$section'";
        $result = mysqli_query($connection, $sql);
        return $result;
    }

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
        $sql_users = "SELECT Users.*, ProjectUsers.is_projectowner, ProjectUsers.user_role FROM Users INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id WHERE ProjectUsers.project_id = $project_id AND ProjectUsers.is_projectowner = 0";
        $result_users = mysqli_query($connection, $sql_users);

        // Get tasks for different sections
        $todo_tasks = get_tasks_by_section($project_id, 'To Do');
        $doing_tasks = get_tasks_by_section($project_id, 'Doing');
        $done_tasks = get_tasks_by_section($project_id, 'Done');

        // Query to get the total number of tasks for the project
        $sql_total_tasks = "SELECT COUNT(*) AS total_tasks FROM Tasks WHERE project_id = $project_id";
        $result_total_tasks = mysqli_query($connection, $sql_total_tasks);
        $row_total_tasks = mysqli_fetch_assoc($result_total_tasks);
        $total_tasks = $row_total_tasks['total_tasks'];

        // Query to get the number of completed tasks for the project
        $sql_completed_tasks = "SELECT COUNT(*) AS completed_tasks FROM Tasks WHERE project_id = $project_id AND status = 'Completed'";
        $result_completed_tasks = mysqli_query($connection, $sql_completed_tasks);
        $row_completed_tasks = mysqli_fetch_assoc($result_completed_tasks);
        $completed_tasks = $row_completed_tasks['completed_tasks'];

        // Query to get the number of incomplete tasks for the project
        $sql_incomplete_tasks = "SELECT COUNT(*) AS incomplete_tasks FROM Tasks WHERE project_id = $project_id AND status != 'Completed'";
        $result_incomplete_tasks = mysqli_query($connection, $sql_incomplete_tasks);
        $row_incomplete_tasks = mysqli_fetch_assoc($result_incomplete_tasks);
        $incomplete_tasks = $row_incomplete_tasks['incomplete_tasks'];

        // Query to get the number of overdue tasks for the project
        $current_date = date('Y-m-d');
        $sql_overdue_tasks = "SELECT COUNT(*) AS overdue_tasks FROM Tasks WHERE project_id = $project_id AND status != 'Completed' AND end_date < '$current_date'";
        $result_overdue_tasks = mysqli_query($connection, $sql_overdue_tasks);
        $row_overdue_tasks = mysqli_fetch_assoc($result_overdue_tasks);
        $overdue_tasks = $row_overdue_tasks['overdue_tasks'];

        echo $user['project_owner'];


    } else {
        echo "Project not found.";
    }
    ?>

    <style>
        .invite-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .invite-popup {
            display: none;
            background-color: var(--overlay-bgcolor);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .invite-popup-content {
            background-color: var(--color-background-weak);
            color: var(--color-text);
            max-width: 400px;
            margin: 100px auto;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .invite-popup-close {
            font-size: 30px;
            float: right;
            cursor: pointer;
        }

        .invite-popup .form-group {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .invite-popup label {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .invite-popup input[type="email"],
        .invite-popup textarea {
            width: 100%;
            border: 1px solid #ccc;
            padding: 8px 5px;
            border-radius: 4px;
            background-color: var(--color-background-weak);
            color: var(--color-text);
        }

        .invite-popup .invite-submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Styling for the user-role-container */
        .user-role-container {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Styling for the "+" sign */
        .border-around-plus {
            border: 1px solid #ccc;
            padding: 2px 6px;
            margin-right: 5px;
        }

        .addtask-btn {
            outline: 0;
            padding: 3px;
            border: 1px solid var(--color-border);
            border-radius: 5px;
            color: var(--color-text-weak);
            background-color: var(--color-background-weak);
        }

        .addtask-btn:hover {
            background-color: var(--color-background-hover);
            color: var(--color-text);
        }

        .addtask-popup {
            display: none;
            background-color: var(--overlay-bgcolor);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .addtask-popup-content {
            background-color: var(--color-background-weak);
            color: var(--color-text);
            max-width: 400px;
            margin: 100px auto;
            padding: 10px 20px;
            border-radius: 4px;
        }

        .addtask-popup-close {
            font-size: 30px;
            float: right;
            cursor: pointer;
        }

        .addtask-popup .form-group {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .addtask-popup label {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .addtask-popup textarea {
            width: 100%;
            border: 1px solid #ccc;
            padding: 8px 5px;
            border-radius: 4px;
            background-color: var(--color-background-weak);
            color: var(--color-text);
        }

        .addtask-popup .editprofile-submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>

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

                <div class="project-desc-textarea">
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
                        // Display project owner's details first
                        if ($project_owner) {
                            echo "<div class='user-content' data-user-id='" . $user['user_id'] . "' id='role_popup-btn'>";
                            echo "<img class='profile-picture' src='./static/image/test.JPG' alt='Profile Picture'>";
                            echo "<div class='profile-info'>";
                            echo "<p class='user-name'>" . $project_owner['username'] . "</p>";
                            echo "<p class='user-role'>Project owner</p>";
                            echo "</div>";
                            echo "</div>";
                        }

                        // Display other users associated with the project
                        if (mysqli_num_rows($result_users) > 0) {
                            echo "<div class='user-role-container'>";
                            // Loop through the remaining users associated with the project
                            while ($user = mysqli_fetch_assoc($result_users)) {
                                echo "<div class='user-content' data-user-id='" . $user['user_id'] . "' data-user-role='" . $user['user_role'] . "' id='role_popup-btn'>";
                                echo "<img class='profile-picture' src='./static/image/test.JPG' alt='Profile Picture'>";
                                echo "<div class='profile-info'>";
                                echo "<p class='user-name'>" . $user['username'] . "</p>";
                                if ($user['user_role']) {
                                    $user_role = $user['user_role'];
                                    echo "<p class='user-role'>$user_role</p>";
                                } else {
                                    echo "<p class='user-role'>+ Add role</p>";
                                }
                                echo "</div>";
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                        ?>
                        <div class="role_popup-menu" id="userrole_popup">
                            <ul>
                                <li>
                                    <form method="post" action="partial/update_userrole.php" class="update_userrole">
                                        <div class="input-container">
                                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                            <input type="hidden" name="user_id" value="">
                                            <input type="text" name="user-role">
                                            <button type="submit" name="update_userrole"
                                                class="relative-button">Done</button>
                                        </div>
                                    </form>
                                <li>
                                    <form method="post" action="partial/remove_user_from_project.php"
                                        class="remove-user-from-proj">
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <input type="hidden" name="user_id" value="">
                                        <button type="submit" class="indicate-danger" name="remove_user">Remove
                                            User</button>
                                    </form>
                                </li>
                            </ul>
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
                            <button type="submit" name="submit" class="btn-style">Submit</button>
                            <!-- <button type="submit" name="submit" class="editprofile-submit-btn">Submit</button> -->
                        </form>
                    </div>
                </div>
                <div class="lst-of-tasks div-space-top">
                    <?php
                    // Check if the 'project_id' parameter is present in the URL
                    if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
                        $project_id = $_GET['project_id'];

                        // Prepare and execute the SQL query using a prepared statement
                        $stmt = $connection->prepare("SELECT * FROM Tasks WHERE project_id = ?");
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
                            <input type="text" id="editTaskName" name="task_name" required>
                            <br>

                            <label for="editAssignee">Assignee:</label>
                            <textarea id="editAssignee" name="assignee" required></textarea>
                            <br>

                            <label for="editTaskDescription">Task Description:</label>
                            <textarea id="editTaskDescription" name="task_description" required></textarea>
                            <br>

                            <label for="editStartDate">Start Date:</label>
                            <input type="date" id="editStartDate" name="start_date" required>
                            <br>

                            <label for="editEndDate">End Date:</label>
                            <input type="date" id="editEndDate" name="end_date" required>
                            <br>

                            <label for="editStatus">Status:</label>
                            <select id="editStatus" name="status" required>
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

                            <label for="editPriority">Priority:</label>
                            <select id="editPriority" name="priority" required>
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
<script>
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
                taskname:taskname
            },
            dataType: "json",
            success: function(response) {
                if (response.status === "error") {
                    $("#taskname-error").text(response.message)
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });

        // Prevent the default form submission
        return false;
    }
</script>