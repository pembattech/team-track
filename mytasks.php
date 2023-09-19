<?php include 'access_denied.php'; ?>


<title>Tasks - TeamTrack</title>

<style>
    .incomplete-tasks-table th,
    .incomplete-tasks-table td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    .incomplete-tasks-table th {
        background-color: #f2f2f2;
    }

    .task-new {
        font-weight: bold;
        color: blue;
    }
</style>

<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>
    <?php
    // Start a session to access session variables (if needed)
    session_start();

    // Check if the user ID is set in the session
    if (isset($_SESSION['user_id'])) {
        // Get the user ID of the logged-in user
        $user_id = $_SESSION['user_id'];

        // Function to get all incomplete tasks assigned to the logged-in user
        function get_incomplete_tasks_for_user($user_id)
        {
            global $connection;

            $sql = "SELECT Tasks.*, Projects.project_name, Users.username 
                FROM Tasks
                INNER JOIN ProjectUsers ON Tasks.projectuser_id = ProjectUsers.projectuser_id
                INNER JOIN Projects ON ProjectUsers.project_id = Projects.project_id
                INNER JOIN Users ON ProjectUsers.user_id = Users.user_id
                WHERE Tasks.status != 'Complete' AND ProjectUsers.user_id = $user_id
                ORDER BY Tasks.start_date DESC";

            $result = mysqli_query($connection, $sql);
            return $result;
        }

        // Get all incomplete tasks for the logged-in user
        $incomplete_tasks = get_incomplete_tasks_for_user($user_id);
    }
    ?>

    <div class="main-content">
        <div class="heading-content">
            <div class="heading-style">
                <p>My Tasks</p>
            </div>

            <div class="tab-btns">
                <!-- Tab Buttons -->
                <div class="heading-nav between-verticle-line tab-btn active" onclick="openTab(event, 'tab1')">List
                </div>
                <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab2')">Files</div>
            </div>
        </div>
        <div class="bottom-line"></div>

        <div class="div-space-top tab-content active" id="tab1">
            <div class="task-list-container">
                <?php
                if (mysqli_num_rows($incomplete_tasks) > 0) {
                    echo "<table class='incomplete-tasks-table'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th class='mytasks-heading'>Task Name</th>";
                    echo "<th class='mytasks-heading'>Project Name</th>";
                    echo "<th class='mytasks-heading'>Assignee</th>";
                    echo "<th class='mytasks-heading'>End Date</th>";
                    echo "<th class='mytasks-heading'>Priority</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody id='task-list'>";
                    while ($task = mysqli_fetch_assoc($incomplete_tasks)) {
                        echo "<tr class='$displayClass'>";
                        echo "<td data-task-id='" . $task['task_id'] . "'>" . add_ellipsis($task['task_name'], 15) . "</td>";
                        echo "<td>" . add_ellipsis($task['project_name'], 15) . "</td>";
                        echo "<td>" . $task['username'] . "</td>";
                        echo "<td>" . ($task['end_date'] ? $task['end_date'] : "n/a") . "</td>";
                        echo "<td>" . ($task['priority'] ? $task['priority'] : "n/a") . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No task assign to this user.</p>";
                }
                ?>
            </div>
        </div>

        <div class="tab-content div-space-top" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    // Function to check for new tasks every 5 seconds
    function checkForNewTasks() {
        $.ajax({
            type: "POST",
            url: "partial/get_new_tasks.php", // PHP script to fetch new tasks
            data: {},
            success: function (response) {
                try {
                    // Parse the JSON response to get the new task IDs
                    const newTaskIds = JSON.parse(response);

                    // Mark new tasks in the table
                    $("#task-list tr td").each(function () {
                        const taskId = $(this).data("task-id");
                        if (newTaskIds.includes(taskId)) {
                            $(this).addClass("task-new");
                        }
                    });
                } catch (error) {
                    console.log("Error parsing JSON response: " + error);
                }
            },
            error: function (xhr, status, error) {
                console.log("Error while checking for new tasks." + error);
            }
        });
    }

    // Check for new tasks initially and then every 5 seconds
    checkForNewTasks();
    setInterval(checkForNewTasks, 5000);
</script>