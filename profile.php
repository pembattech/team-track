<?php include 'access_denied.php'; ?>

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

    .editprofile-popup {
        display: none;
        background-color: var(--overlay-bgcolor);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .editprofile-popup-content {
        background-color: var(--color-background-weak);
        color: var(--color-text);
        max-width: 400px;
        margin: 100px auto;
        padding: 10px 20px;
        border-radius: 4px;
    }

    .editprofile-popup-close {
        font-size: 30px;
        float: right;
        cursor: pointer;
    }

    .editprofile-popup .form-group {
        margin-bottom: 15px;
        font-size: 14px;
    }

    .editprofile-popup label {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .editprofile-popup textarea {
        width: 100%;
        border: 1px solid #ccc;
        padding: 8px 5px;
        border-radius: 4px;
        background-color: var(--color-background-weak);
        color: var(--color-text);
    }

    .editprofile-popup .editprofile-submit-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
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

    .editproject-popup {
        display: none;
        background-color: var(--overlay-bgcolor);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .editproject-popup-content {
        background-color: var(--color-background-weak);
        color: var(--color-text);
        max-width: 400px;
        margin: 100px auto;
        padding: 10px 20px;
        border-radius: 4px;
    }

    .editproject-popup-close {
        font-size: 30px;
        float: right;
        cursor: pointer;
    }

    .editproject-popup .form-group {
        margin-bottom: 15px;
        font-size: 14px;
    }

    .editproject-popup label {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .editproject-popup textarea {
        width: 100%;
        border: 1px solid #ccc;
        padding: 8px 5px;
        border-radius: 4px;
        background-color: var(--color-background-weak);
        color: var(--color-text);
    }

    .editproject-popup .editproject-submit-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
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
                    <button class="edit-profile-btn" onclick="editprofile_popup_toggle()">Edit Profile</button>
                </div>
            </div>
            <div class="editprofile-popup" id="editprofile-popup">
                <div class="editprofile-popup-content">
                    <form action="partial/update_profile.php" method="post" enctype="multipart/form-data">
                        <span class="editprofile-popup-close" onclick="editprofile_popup_toggle()">&times;</span>
                        <p class="heading-style">Edit Profile</p>

                        <div class="form-group">
                            <div class="profile-picture">
                                <img src="https://bootdey.com/img/Content/avatar/avatar7.png" id="prof-pic"
                                    alt="profile-pic" width="150">
                                <label class="change-pic-lbl text-primary" for="file">Change Profile Photo</label>
                                <input class="d-none" id="file" name="new-profilepic" type="file"
                                    onchange="loadImgFile(event)" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="about">About</label>
                            <textarea id="about" name="about" rows="4"
                                placeholder="Please provide a brief introduction about yourself."><?php if (get_user_data($user_id)['about'] !== null && get_user_data($user_id)['about'] !== "") {
                                    echo get_user_data($user_id)['about'];
                                }
                                ?></textarea>
                        </div>
                        <button type="submit" name="submit" class="editprofile-submit-btn">Submit</button>
                    </form>
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
                <div class="project div-space-top">
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
                                echo '<button onclick="editproject_popup_toggle(' . $project_id . ')">Edit</button>';
                                echo '<a href="partial/delete_project.php?project_id=' . $project_id . '"><button>Delete</button></a>';
                                echo '</div>';
                                echo '</div>';
                            }

                        } else {
                            // If no projects are assigned, display a message or do something else
                            echo '<p>No projects assigned to this user.</p>';
                        }
                    }
                    ?>
                    <div class="editproject-popup" id="editproject-popup">
                        <div class="editproject-popup-content">
                            <form action="partial/edit_project.php" method="post" enctype="multipart/form-data">
                                <span class="editproject-popup-close"
                                    onclick="editproject_popup_toggle()">&times;</span>
                                <p class="heading-style">Edit Profile</p>
                                <div class="form-group">
                                    <input type="hidden" name="project_id" id="project_id" value="">
                                    <label for="project_name">project_name</label>
                                    <input type="text" name="project_name" id="project_name"
                                        value="<?php echo get_project_data($project_id)['project_name'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="description">description</label>
                                    <textarea type="text" name="description"
                                        id="description"><?php echo get_project_data($project_id)['description'] ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">start_date</label>
                                    <input type="text" name="start_date" id="start_date"
                                        value="<?php echo get_project_data($project_id)['start_date'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="end_date">end_date</label>
                                    <input type="text" name="end_date" id="end_date"
                                        value="<?php echo get_project_data($project_id)['end_date'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="status">status</label>
                                    <input type="text" name="status" id="status"
                                        value="<?php echo get_project_data($project_id)['status'] ?>">
                                </div>
                                <button type="submit" name="submit" class="editproject-submit-btn">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editprofile_popup_toggle() {
        const popup = document.getElementById('editprofile-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }
</script>
<script>
    function editproject_popup_toggle(projectId) {
        const popup = document.getElementById('editproject-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';

        if (popup.style.display === 'block') {
            // If the popup is being displayed, set the value of the hidden input field to the project ID
            document.getElementById('project_id').value = projectId;

            // Fetch project data using AJAX
            fetchProjectData(projectId);
        }
    }

    function fetchProjectData(projectId) {
        // Send an AJAX request to fetch project data
        fetch('partial/fetch_project_data.php?project_id=' + projectId)
            .then(response => response.json())
            .then(data => {
                // Populate the description field with the fetched data
                document.getElementById('project_name').value = data.project_name;
                document.getElementById('description').value = data.description;
                document.getElementById('start_date').value = data.start_date;
                document.getElementById('end_date').value = data.end_date;
                document.getElementById('status').value = data.status;
            })
            .catch(error => console.error('Error fetching project data:', error));
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