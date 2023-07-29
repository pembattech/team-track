<title>Profile - TeamTrack</title>

<?php include 'partial/navbar.php'; ?>
<style>
    .userinfo-profilepic {
        display: flex;
        flex-direction: row;
    }

    .profile-picture img,
    .profile-picture #popup-btn {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        font-size: 100px;
    }

    .form-group .profile-picture img {
        width: 150px;
        height: 150px;
    }

    .user-personal-info {
        margin: 50px 0 0 20px;
        justify-content: center;
    }

    .user-personal-info .user-name p {
        font-size: 30px;
        color: var(--color-text);
    }

    .user-personal-info .user-email p {
        font-size: 14px;
        color: var(--color-text-weak);
    }

    .user-other-info {
        margin-top: 10px;
    }

    .user-other-info-grid {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 20px;
    }


    .user-other-info .user-about {
        width: 400px;
        height: auto;
    }

    .user-other-info .user-about,
    .user-other-info .user-tasks,
    .user-other-info .user-project {
        padding: 0 5px;
        background-color: var(--sidebar-bgcolor);
        border: 1px solid var(--color-border);
        border-radius: 5px;
        block
    }

    .user-other-info .heading-style {
        padding: 10px 0;
        margin-bottom: 0;
    }

    .user-other-info .user-tasks {
        width: 100%;
    }

    .edit-profile-btn {
        outline: 0;
        padding: 3px;
        border: 1px solid var(--color-border);
        border-radius: 5px;
        color: var(--color-text-weak);
        background-color: var(--color-background-weak);
    }

    .edit-profile-btn:hover {
        background-color: var(--color-background-hover);
        color: var(--color-text);
    }

    input.d-none {
        display: none;
    }

    .change-pic-lbl {
        display: block;
        cursor: pointer;
        padding: 10px;
        margin-top: 10px;
        width: 37%;
        border: 1px solid var(--color-border);
        border-radius: 5px;
        color: var(--color-text-weak);
        background-color: var(--color-background-weak);
    }

    .change-pic-lbl:hover {
        background-color: var(--color-background-hover);
        color: var(--color-text);
    }

    

    .user-project .project .project-lst {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .user-project .project-options {
        padding: 0 15px;
    }

    .user-project .project-options button {
        outline: 0;
        padding: 0 10px;
        border: 1px solid var(--color-border);
        border-radius: 5px;
        color: var(--color-text-weak);
        background-color: var(--color-background-weak);
    }

    .user-project .project-options button:hover {
        background-color: var(--color-background-hover);
        color: var(--color-text);
    }
</style>
<div class="container userprofile-wrapper">
    <?php include 'partial/sidebar.php'; ?>
    <div class='main-content'>
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
                    INNER JOIN Projects ON Tasks.project_id = Projects.project_id
                    INNER JOIN Users ON Tasks.user_id = Users.user_id
                    WHERE Tasks.status != 'Completed' AND Tasks.user_id = $user_id
                    ORDER BY Tasks.start_date DESC";

                $result = mysqli_query($connection, $sql);
                return $result;
            }

            // Get all incomplete tasks for the logged-in user
            $incomplete_tasks = get_incomplete_tasks_for_user($user_id);
        }
        ?>


        <div class="userinfo-profilepic">

            <div class="profile-picture">
                <?php display_profile_picture($user_id); ?>
            </div>

            <div class="user-personal-info">
                <div class="user-name">
                    <p>
                        <?php echo get_user_data($user_id)['name']; ?>
                    </p>
                </div>
                <div class="user-email">
                    <p>
                        <?php echo get_user_data($user_id)['email']; ?>
                    </p>
                    <button class="edit-profile-btn" onclick="editproject_popup_toggle()">Edit Profile</button>
                </div>
            </div>
            
        </div>

        <div class="user-other-info">
            <div class="user-other-info-grid">
                <div class="user-tasks">
                    <div class="heading-content">
                        <div class="heading-style">
                            <p>My Tasks</p>
                        </div>
                    </div>
                    <div class="bottom-line"></div>
                    <?php
                    if (mysqli_num_rows($incomplete_tasks) > 0) {
                        // Display the table with all incomplete tasks for the user
                        echo "<table class='incomplete-tasks-table'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th class='mytasks-heading'>Task Name</th>";
                        echo "<th class='mytasks-heading'>Project Name</th>";
                        echo "<th class='mytasks-heading'>Assignee</th>";
                        echo "<th class='mytasks-heading'>Due Date</th>";
                        echo "<th class='mytasks-heading'>Priority</th>";
                        echo "<th class='mytasks-heading'>Status</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody id='task-list'>"; // Added ID for the tbody element
                        // Loop through all incomplete tasks
                        while ($task = mysqli_fetch_assoc($incomplete_tasks)) {
                            echo "<tr>";
                            echo "<td data-task-id='" . $task['task_id'] . "'>" . $task['task_name'] . "</td>";
                            echo "<td>" . $task['project_name'] . "</td>";
                            echo "<td>" . $task['username'] . "</td>";
                            echo "<td>" . $task['end_date'] . "</td>";
                            echo "<td>" . $task['priority'] . "</td>";
                            echo "<td>" . $task['status'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";

                    }
                    ?>
                </div>

                <div class="user-about overlay-border">
                    <div class="heading-content">
                        <div class="heading-style">
                            <p>About</p>
                        </div>
                    </div>
                    <div class="bottom-line"></div>
                    <div class="about">
                        <p>
                            <?php if (get_user_data($user_id)['about'] !== null && get_user_data($user_id)['about'] !== "") {
                                echo get_user_data($user_id)['about'];
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="user-project overlay-border div-space-top">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>Projects</p>
                    </div>
                </div>
                <div class="bottom-line"></div>
                <div class="project">
                    <?php
                    // Start a session to access session variables (if needed)
                    session_start();

                    // Check if the user ID is set in the session
                    if (isset($_SESSION['user_id'])) {
                        // Get the user ID of the logged-in user
                        $user_id = $_SESSION['user_id'];

                        // Fetch project names from the "Projects" table where the user is assigned
                        $sql = "SELECT P.project_id, P.project_name, P.background_color 
                        FROM Projects P
                        INNER JOIN ProjectUsers PU ON P.project_id = PU.project_id
                        WHERE PU.user_id = $user_id";

                        $result = $connection->query($sql);

                        if ($result->num_rows > 0) {
                            // Loop through the results and generate anchor tags for each project
                            while ($row = $result->fetch_assoc()) {
                                $project_id = $row['project_id'];
                                $project_name = $row['project_name'];
                                $background_color = $row['background_color'];
                                echo '<div class="project-lst">';
                                echo '<div>';
                                echo '<a href="project.php?project_id=' . $project_id . '" class="project-link">';
                                echo '    <div class="square" style="background-color:' . $background_color . '"></div>';
                                echo '    <p class="project-title">' . $project_name . '</p>';
                                echo '</a>';
                                echo '</div>';
                                echo '<div class="project-options">';
                                echo '<button>Edit</button>';
                                echo '<a href="partial/delete_project.php?project_id=' . $project_id . '"><button>Delete</button></a>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            // If no projects are assigned, display a message or do something else
                            echo 'No projects assigned to this user.';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editproject_popup_toggle() {
            const popup = document.getElementById('editproject-popup');
            popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
        }
    </script>
    <script>
        // EVENT LISTENERS
        document.addEventListener('DOMContentLoaded', function () {
            // disable input elements on load
            toggleFormElements(1);
        });
        // upload profile image
        var loadImgFile = function (event) {
            var image = document.getElementById("prof-pic");
            image.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>