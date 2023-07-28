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
        margin-top: 30px;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 50px;
    }


    .user-other-info .user-about {
        width: 400px;
        height: auto;
    }

    .user-other-info .user-about,
    .user-other-info .user-tasks {
        padding: 0 5px;
        background-color: var(--sidebar-bgcolor);
        border: 1px solid var(--color-border);
        border-radius: 5px;
    }

    .user-other-info .user-about .heading-style,
    .user-other-info .user-tasks .heading-style {
        padding: 10px 0;
        margin-bottom: 0;
    }

    .user-other-info .user-tasks {
        width: 100%;
    }

    .editprofile-popup {
        display: block;
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
        overflow-y: auto;
        overflow-x: auto;
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
</style>
<div class="container userprofile-wrapper">
    <?php include 'partial/sidebar.php'; ?>
    <?php
    // Start a session to access session variables (if needed)
    session_start();

    // Check if the user ID is set in the session
    if (isset($_SESSION['user_id'])) {
        // Get the user ID of the logged-in user
        $user_id = $_SESSION['user_id'];

        echo $user_id;

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

    <div class='main-content'>

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
                    <span class="editprofile-popup-close" onclick="editprofile_popup_toggle()">&times;</span>
                    <p class="heading-style">Edit Profile</p>

                    <form>
                        <div class="form-group">
                            <div class="profile-picture">
                                <img src="https://bootdey.com/img/Content/avatar/avatar7.png" id="prof-pic"
                                    alt="profile-pic" class="rounded-circle border p-2" width="150">
                                <label class="change-pic-lbl text-primary" for="file">Change Profile Photo</label>
                                <input class="d-none" id="file" type="file" onchange="loadImgFile(event)" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="about">About</label>
                            <textarea id="about" name="about" rows="4" placeholder="Please provide a brief introduction about yourself."></textarea>
                        </div>
                        <button type="submit" class="editprofile-submit-btn">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="user-other-info">
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

                <p>hello this is me</p>`
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