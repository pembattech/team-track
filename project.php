<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>
    <?php

    // Function to sanitize input to prevent SQL injection
    function sanitize_input($input)
    {
        global $connection;
        return mysqli_real_escape_string($connection, $input);
    }

    // Function to get tasks for a specific project and section
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

        // Query to fetch project details from the 'Projects' table
        $sql_project = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result_project = mysqli_query($connection, $sql_project);

        if (mysqli_num_rows($result_project) > 0) {
            $project = mysqli_fetch_assoc($result_project);
            echo "<div class='main-content'>";
            echo "<div class='heading-content'>";
            echo "<div class='heading-style'>";
            echo "<p>" . $project['project_name'] . "</p>";
            echo "</div>";
            echo "</div>";

            // Query to fetch users associated with the project from the 'ProjectUsers' table
            $sql_users = "SELECT Users.* FROM Users
                      INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
                      WHERE ProjectUsers.project_id = $project_id";
            $result_users = mysqli_query($connection, $sql_users);

        } else {
            echo "Project not found.";
        }

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

        // Get tasks for different sections
        $todo_tasks = get_tasks_by_section($project_id, 'To Do');
        $doing_tasks = get_tasks_by_section($project_id, 'Doing');
        $done_tasks = get_tasks_by_section($project_id, 'Done');
    } else {
        echo "Invalid project ID.";
    }
    ?>


    <style>
        /* Styles for collapsible task sections */
        .tasks .task-section {
            cursor: pointer;
            font-weight: bold;
            background-color: #f2f2f2;
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            align-items: left;
        }

        .task-section.active {
            background-color: #ddd;
        }

        /* Styles for draggable tasks */
        .tasks .task {
            cursor: move;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ccc;
            padding: 10px;
        }

        /* Placeholder style for dragged tasks */
        .ui-sortable-placeholder {
            background-color: #e2e2e2;
            border: 1px dashed #999;
            height: 40px;
        }
    </style>



    <div class="tabset">
        <!-- Tab 1 -->
        <input type="radio" name="tabset" id="tab1" aria-controls="overview" checked>
        <label for="tab1">Overview</label>
        <!-- Tab 2 -->
        <input type="radio" name="tabset" id="tab2" aria-controls="list">
        <label for="tab2">List</label>
        <!-- Tab 3 -->
        <input type="radio" name="tabset" id="tab3" aria-controls="dashboard">
        <label for="tab3">Dashboard</label>
        <!-- Tab 4 -->
        <input type="radio" name="tabset" id="tab4" aria-controls="messages">
        <label for="tab4">Messages</label>
        <!-- Tab 5 -->
        <input type="radio" name="tabset" id="tab5" aria-controls="files">
        <label for="tab5">Files</label>

        <div class="tab-panels">
            <section id="overview" class="tab-panel">
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
            </section>
            <section id="list" class="tab-panel">
                <div class="active heading-nav-content div-space-top">
                    <div class="addtask-btn overlay-border">
                        <span class="plus">+</span>
                        <span>Add Task</span>
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
                        </tbody>
                    </table>
                </div>
            </section>
            <section id="dashboard" class="tab-panel">
                <div class="dashboard-container div-space-top">
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
            </section>
            <section id="messages" class="tab-panel">
                <h2>Tab 4</h2>
            </section>
            <section id="files" class="tab-panel">
                <h2>Tab 5</h2>
            </section>
        </div>
    </div>
</div>

<script>
    // Function to toggle collapsible sections
    function toggleCollapse(className) {
        var rows = document.getElementsByClassName(className);
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            if (row.style.display === "none") {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        }
    }

    // Show collapsed rows by default
    var initialCollapsedRows = document.getElementsByClassName('collapsed');
    for (var i = 0; i < initialCollapsedRows.length; i++) {
        initialCollapsedRows[i].style.display = "table-row";
    }

    // Function to update task section and status via AJAX
    function updateTaskSection(task_id, section) {
        // Send AJAX request to update task section in the database
        $.ajax({
            type: "POST",
            url: "update_task_section.php", // Replace with the PHP script to handle the update
            data: {
                task_id: task_id,
                section: section
            },
            success: function (response) {
                // Handle the response from the server if needed

                // Check if the new section is "Done" and update status if true
                if (section === "Done") {
                    updateTaskStatus(task_id, "Completed");
                } else {
                    updateTaskStatus(task_id, "Incomplete");
                }
            },
            error: function (xhr, status, error) {
                // Handle errors if the update fails
            }
        });
    }

    // Function to update task status via AJAX
    function updateTaskStatus(task_id, status) {
        // Send AJAX request to update task status in the database
        $.ajax({
            type: "POST",
            url: "update_task_status.php", // PHP script to handle the status update
            data: {
                task_id: task_id,
                status: status
            },
            success: function (response) {
                // Handle the response from the server if needed
            },
            error: function (xhr, status, error) {
                // Handle errors if the status update fails
            }
        });
    }

    // Document ready event to initialize the drag and drop functionality
    $(document).ready(function () {
        // Initialize the collapsible sections
        $(".collapsible").click(function () {
            $(this).toggleClass("active");
            var content = $(this).next();
            if (content.css("display") === "block") {
                content.css("display", "none");
            } else {
                content.css("display", "block");
            }
        });

        // Enable drag and drop functionality
        $(".sortable").sortable({
            connectWith: ".sortable",
            placeholder: "ui-sortable-placeholder",
            start: function (event, ui) {
                ui.item.addClass("ui-draggable-dragging");
            },
            stop: function (event, ui) {
                ui.item.removeClass("ui-draggable-dragging");
                var task_id = ui.item.data("task-id");
                var new_section = ui.item.closest(".sortable").attr("id");
                updateTaskSection(task_id, new_section);
            }
        });
    });
</script>