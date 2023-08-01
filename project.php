<!-- <?php include 'access_denied.php'; ?> -->
<title>Project - TeamTrack</title>

<?php include 'partial/navbar.php'; ?>
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
        $sql_owner = "SELECT Users.*, ProjectUsers.project_owner, ProjectUsers.user_role FROM Users INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id WHERE ProjectUsers.project_id = $project_id AND ProjectUsers.project_owner = 1";
        $result_owner = mysqli_query($connection, $sql_owner);
        $project_owner = mysqli_fetch_assoc($result_owner);

        // Query to fetch other users associated with the project from the 'ProjectUsers' table (excluding the owner)
        $sql_users = "SELECT Users.*, ProjectUsers.project_owner, ProjectUsers.user_role FROM Users INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id WHERE ProjectUsers.project_id = $project_id AND ProjectUsers.project_owner = 0";
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
            gap: 25px;
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
                    // Check if a description exists
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
                            echo "<div class='user-content'>";
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
                                echo "<div class='user-content'>";
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

                        <div class="invite-popup" id="invite-popup">
                            <div class="invite-popup-content">
                                <span class="invite-popup-close" onclick="addmember_popup_toggle()">&times;</span>
                                <p class="heading-style">Share '
                                    <?php echo $project['project_name'] ?>'
                                </p>

                                <form>
                                    <div class="form-group">
                                        <p>Invite with email</p>
                                        <input type="email" iwd="email" name="email"
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
            <div class="tab-content div-space-top" id="tab2">
                <div class="tasks-section">
                    <!-- <div class="addtask-popup" onclick="addtask_popup_toggle()">
                    <div class="heading-nav-content addtask-btn overlay-border">
                            <span class="plus">+</span>
                            <span>Add Task</span>
                    </div>
                </div> -->

                    <button class="addtask-btn" onclick="addtask_popup_toggle()">+ Add Task</button>

                    <div class="addtask-popup" id="addtask-popup">
                        <div class="addtask-popup-content">
                            <form action="partial/addtask.php" method="post" enctype="multipart/form-data">
                                <span class="addtask-popup-close" onclick="addtask_popup_toggle()">&times;</span>
                                <p class="heading-style">Add Task</p>
                                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                <div class="form-group">
                                    <label for="taskname">Task Name</label>
                                    <input type="text" name="taskname">
                                </div>
                                <div class="form-group">
                                    <label for="assignee">Assignee</label>
                                    <input type="text" name="assignee">
                                </div>
                                <!-- <div class="form-group">
                                <label for="duedate">Due Date</label>
                                <input type="text" name="duedate">
                            </div> -->
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <input type="text" name="priority">
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" name="status">
                                </div>
                                <button type="submit" name="submit" class="editprofile-submit-btn">Submit</button>
                            </form>
                        </div>
                    </div>

                    <!-- Draggable Tasks Lists -->
                    <div class="tasks div-space-top">
                        <table>
                            <thead>
                                <tr>
                                    <th class="mytasks-heading">Task Name</th>
                                    <th class="mytasks-heading">Assignee</th>
                                    <th class="mytasks-heading">Due Date</th>
                                    <th class="mytasks-heading">Priority</th>
                                    <th class="mytasks-heading">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- To Do -->
                                <tr>
                                    <td colspan="5" class="collapsible task-section">To Do</td>
                                </tr>
                            <tbody class="sortable" id="To Do">
                                <?php
                                while ($task = mysqli_fetch_assoc($todo_tasks)) {
                                    echo '<tr class="task" data-task-id="' . $task['task_id'] . '">';
                                    echo '<td class="task-name">' . $task['task_name'] . '</td>';
                                    echo '<td>' . $task['user_id'] . '</td>';
                                    echo '<td>' . $task['end_date'] . '</td>';
                                    echo '<td>' . $task['priority'] . '</td>';
                                    echo '<td>' . $task['status'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <!-- Doing -->
                            <tr>
                                <td colspan="5" class="collapsible task-section">Doing</td>
                            </tr>
                            <tbody class="sortable" id="Doing">
                                <?php
                                while ($task = mysqli_fetch_assoc($doing_tasks)) {
                                    echo '<tr class="task" data-task-id="' . $task['task_id'] . '">';
                                    echo '<td class="task-name">' . $task['task_name'] . '</td>';
                                    echo '<td>' . $task['user_id'] . '</td>';
                                    echo '<td>' . $task['end_date'] . '</td>';
                                    echo '<td>' . $task['priority'] . '</td>';
                                    echo '<td>' . $task['status'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <!-- Done -->
                            <tr>
                                <td colspan="5" class="collapsible task-section">Done</td>
                            </tr>
                            <tbody class="sortable" id="Done">
                                <?php
                                while ($task = mysqli_fetch_assoc($done_tasks)) {
                                    echo '<tr class="task" data-task-id="' . $task['task_id'] . '">';
                                    echo '<td class="task-name">' . $task['task_name'] . '</td>';
                                    echo '<td>' . $task['user_id'] . '</td>';
                                    echo '<td>' . $task['end_date'] . '</td>';
                                    echo '<td>' . $task['priority'] . '</td>';
                                    echo '<td>' . $task['status'] . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
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