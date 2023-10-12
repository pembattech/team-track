<?php
// Include the access_denied.php file before any HTML output
include 'access_denied.php';

// Set the title inside the HTML head
echo '<title>Tasks - TeamTrack</title>';

// Include the required CSS styles within the head
echo '<style>
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
</style>';

// Include the partial/navbar.php file before the container div
include 'partial/navbar.php';
?>

<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <?php
    // // Enable error reporting
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    // Start a session to access session variables (if needed)
    session_start();

    <?php
// Include the access_denied.php file before any HTML output
include 'access_denied.php';

// Set the title inside the HTML head
echo '<title>Tasks - TeamTrack</title>';

// Include the required CSS styles within the head
echo '<style>
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
</style>';

// Include the partial/navbar.php file before the container div
include 'partial/navbar.php';
?>

<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <?php
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Start a session to access session variables (if needed)
    session_start();

    // Check if the user ID is set in the session
    if (isset($_SESSION['user_id'])) {
        // Get the user ID of the logged-in user
        $user_id = $_SESSION['user_id'];

        // Function to get all incomplete tasks assigned to the logged-in user
        function get_incomplete_tasks_for_user($user_id, $connection)
        {
            $sql = "SELECT
                t.*,
                u.username AS CreatorName,
                p.project_name AS project_name
            FROM
                Tasks t
            JOIN
                Users u ON t.task_creator_id = u.user_id
            JOIN
                ProjectUsers pu ON t.projectuser_id = pu.projectuser_id
            JOIN
                Projects p ON pu.project_id = p.project_id
            WHERE
                t.status <> 'Complete'
                AND t.status <> 'New'
                AND t.assignee = $user_id
            ORDER BY
                t.task_id DESC";

            $result = mysqli_query($connection, $sql);
            return $result;
        }

        // Get all incomplete tasks for the logged-in user
        $incomplete_tasks = get_incomplete_tasks_for_user($user_id, $connection);
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
                <!-- <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab2')">Files</div> -->
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
                    echo "<th class='mytasks-heading'>End Date</th>";
                    echo "<th class='mytasks-heading'>Priority</th>";
                    echo "<th class='mytasks-heading'>Task Creator</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody id='task-list'>";
                    while ($task = mysqli_fetch_assoc($incomplete_tasks)) {
                        echo "<tr>";
                        echo "<td data-task-id='" . $task['task_id'] . "'>" . add_ellipsis($task['task_name'], 15) . "</td>";
                        echo "<td>" . add_ellipsis($task['project_name'], 15) . "</td>";
                        echo "<td>" . ($task['end_date'] ? $task['end_date'] : "n/a") . "</td>";
                        echo "<td>" . ($task['priority'] ? $task['priority'] : "n/a") . "</td>";
                        echo "<td>" . $task['CreatorName'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No task assigned to this user.</p>";
                }
                ?>
            </div>
        </div>

        <!-- <div class="tab-content div-space-top" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div> -->
    </div>
</div>
// Check if the user ID is set in the session
    if (isset($_SESSION['user_id'])) {
        // Get the user ID of the logged-in user
        $user_id = $_SESSION['user_id'];

        // Function to get all incomplete tasks assigned to the logged-in user
        function get_incomplete_tasks_for_user($user_id, $connection)
        {
            $sql = "SELECT
                t.*,
                u.username AS CreatorName,
                p.project_name AS project_name
            FROM
                Tasks t
            JOIN
                Users u ON t.task_creator_id = u.user_id
            JOIN
                ProjectUsers pu ON t.projectuser_id = pu.projectuser_id
            JOIN
                Projects p ON pu.project_id = p.project_id
            WHERE
                t.status <> 'Complete'
                AND t.status <> 'New'
                AND t.assignee = $user_id
            ORDER BY
                t.task_id DESC";

            $result = mysqli_query($connection, $sql);
            return $result;
        }

        // Get all incomplete tasks for the logged-in user
        $incomplete_tasks = get_incomplete_tasks_for_user($user_id, $connection);
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
                <!-- <div class="heading-nav between-verticle-line tab-btn" onclick="openTab(event, 'tab2')">Files</div> -->
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
                    echo "<th class='mytasks-heading'>End Date</th>";
                    echo "<th class='mytasks-heading'>Priority</th>";
                    echo "<th class='mytasks-heading'>Task Creator</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody id='task-list'>";
                    while ($task = mysqli_fetch_assoc($incomplete_tasks)) {
                        echo "<tr>";
                        echo "<td data-task-id='" . $task['task_id'] . "'>" . add_ellipsis($task['task_name'], 15) . "</td>";
                        echo "<td>" . add_ellipsis($task['project_name'], 15) . "</td>";
                        echo "<td>" . ($task['end_date'] ? $task['end_date'] : "n/a") . "</td>";
                        echo "<td>" . ($task['priority'] ? $task['priority'] : "n/a") . "</td>";
                        echo "<td>" . $task['CreatorName'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No task assigned to this user.</p>";
                }
                ?>
            </div>
        </div>

        <!-- <div class="tab-content div-space-top" id="tab2">
            <h3>Tab 2 Content</h3>
            <p>This is the content of Tab 2.</p>
        </div> -->
    </div>
</div>
