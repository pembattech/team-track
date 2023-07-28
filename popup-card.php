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
        display: block;
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
</style>

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
        <div class="invite-popup" id="invite-popup">
            <div class="invite-popup-content">
                <span class="invite-popup-close" onclick="addmember_popup_toggle()">&times;</span>
                <p class="heading-style">Share '
                    <?php echo $project['project_name'] ?>'
                </p>

                <form>
                    <div class="form-group">
                        <p>Invite with email</p>
                        <input type="email" iwd="email" name="email" placeholder="Add members by email...">
                    </div>
                    <div class="form-group">
                        <label for="message">Message (optional)</label>
                        <textarea id="message" name="message" rows="4" placeholder="Add a message"></textarea>
                    </div>
                    <button type="submit" class="invite-submit-btn">Send Invite</button>
                </form>
            </div>
        </div>


        <script>
            function addmember_popup_toggle() {
                const popup = document.getElementById('invite-popup');
                popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
            }
        </script>