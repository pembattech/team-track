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
        echo $project_id;

        // Query to fetch project details from the 'Projects' table
        $sql_project = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result_project = mysqli_query($connection, $sql_project);

        // Query to fetch users associated with the project from the 'ProjectUsers' table
        $sql_users = "SELECT Users.* FROM Users
                      INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
                      WHERE ProjectUsers.project_id = $project_id";
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

        <div class="tab-content div-space-top" id="tab1">
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
                    <?php
                    if (mysqli_num_rows($result_users) > 0) {
                        echo "<div class='user-role-container'>";
                        echo "<div class='add-member'>";
                        echo "<p><span class='border-around-plus'>+</span> Add member</p>";
                        echo "</div>";

                        // Loop through the remaining users associated with the project
                        while ($user = mysqli_fetch_assoc($result_users)) {
                            echo "<div class='user-role'>";
                            echo "<img class='profile-picture' src='./static/image/test.JPG' alt='Profile Picture'>";
                            echo "<div class='profile-info'>";
                            echo "<p class='user-name'>" . $user['username'] . "</p>";
                            echo "<p class='user-role'>+ Add role</p>";
                            echo "</div>";
                            echo "</div>";
                        }

                        echo "</div>";
                    } else {
                        echo "<p>No users associated with this project.</p>";
                    }
                    ?>

                </div>

            </div>
        </div>
        <div class="tab-content div-space-top" id="tab2">
            <div class="tasks-section">
                <div class="heading-nav-content addtask-btn overlay-border">
                    <span class="plus">+</span>
                    <span>Add Task</span>
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