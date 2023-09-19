<!-- <?php include 'access_denied.php'; ?> -->


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
        display: flex;
        flex-direction: row;
        gap: 10px;
    }

    .user-about-project {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .user-other-info .about-section {
        width: 400px;
        height: 100%;
        overflow-wrap: anywhere;
    }

    .user-other-info .project-section {
        width: 400px;
        max-width: 400px;
    }

    /* .user-other-info .user-tasks, .user-about-project { */
    .user-other-info .user-tasks,
    .about-section,
    .project-section {
        padding: 0 5px;
        background-color: var(--sidebar-bgcolor);
        border: 1px solid var(--color-border);
        border-radius: 5px;
    }

    .user-other-info .heading-style {
        padding: 10px 0;
        margin-bottom: 0;
    }

    .user-other-info .user-tasks {
        width: 100%;
        flex: 2;
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

    .profile-picture-options {
        display: flex;
        flex-direction: row;
        gap: 10px;
        align-items: center;
    }

    .change-pic-lbl {
        cursor: pointer;
        padding: 8px 3px;
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
        z-index: 9;
    }

    .remove-profile-picture .button-style-w-danger {
        padding: 0 5px;
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
        margin-bottom: 10px;
        font-size: 16px;
    }

    .editprofile-popup textarea {
        width: 100%;
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

    .user-other-info .project {
        width: 100%;
        overflow-wrap: anywhere;
        overflow-y: auto;
        max-height: 380px;
    }

    .user-other-info .project::-webkit-scrollbar {
        width: 5px;
    }

    .user-project .project .project-lst {
        display: grid;
        grid-template-columns: 1fr auto;
        width: 100%;
        align-items: center;
        gap: 10px;
    }

    .user-project .project .project-lst-name:hover {
        border: 0;
        border-radius: 5px;
        background-color: var(--color-background-weak);
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

    .task-list-container {
        max-height: 600px;
        overflow-y: auto;
    }

    .task-list-container th {
        position: sticky;
        top: -1;
        background-color: var(--bg-color);
        transition: top 0.3s ease;

    }

    .task-list-container th,
    .task-list-container td {
        border: 1px solid var(--color-border);
        padding: 5px;
        text-align: left;
    }

    .task-list-container td {
        font-size: 14px;
        width: 20%;
    }

    .user-other-info .project-section
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
            <div class="editprofile-popup popup-style" id="editprofile-popup">
                <div class="editprofile-popup-content">
                    <div class="heading-content">
                        <span class="editprofile-popup-close" onclick="editprofile_popup_toggle()">&times;</span>
                        <p class="heading-style">Edit Profile</p>
                    </div>
                    <div class="bottom-line"></div>
                    <div class="div-space-top"></div>
                    <form action="partial/update_profile.php" method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <div class="profile-picture">
                                <?php
                                $user_data = get_user_data($user_id);
                                $profile_picture = $user_data['profile_picture'];
                                $full_name = $user_data['name'];
                                ?>

                                <?php
                                if ($profile_picture !== null && $profile_picture !== "") {
                                    ?>
                                    <img src="<?php echo $profile_picture ?>" id="prof-pic" alt="profile-pic" width="150">

                                    <?php
                                } else {
                                    // Display the initials of the full name as the profile picture
                                    $name_parts = explode(' ', $full_name);
                                    $initials = '';
                                    foreach ($name_parts as $part) {
                                        $initials .= strtoupper(substr($part, 0, 1));
                                    }

                                    function hexToPercentEncoded($hexColor)
                                    {
                                        // Remove any leading "#" if present
                                        $hexColor = ltrim($hexColor, "#");

                                        if (strlen($hexColor) !== 6) {
                                            return false; // Invalid hex color format
                                        }

                                        $percentEncoded = "%23" . strtoupper($hexColor);

                                        return $percentEncoded;
                                    }

                                    $hexColor = $user_data['background_color'];
                                    $percentEncodedColor = hexToPercentEncoded($hexColor);

                                    $svgImage = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
                                                    <rect width="100%" height="100%" fill="' . $percentEncodedColor . '" />
                                                    <text x="50%" y="50%" dy="0.35em" text-anchor="middle" fill="black" font-size="50" font-family="Arial">' . $initials . '</text>
                                                 </svg>';

                                    // Display the SVG image using data URI
                                    echo '<img id="prof-pic" src="data:image/svg+xml;charset=utf-8,' . htmlspecialchars($svgImage) . '" />';

                                }

                                ?>


                                <div class="div-space-top"></div>
                                <div class="profile-picture-options">
                                    <div class="update-profile-picture">
                                        <label class="change-pic-lbl" for="file">Change Profile Picture</label>
                                        <input class="d-none" id="file" name="new-profilepic" type="file"
                                            onchange="loadImgFile(event)" />
                                    </div>
                                    <div class="remove-profile-picture">
                                        <button type="button" class="button-style-w-danger indicate-danger"
                                            id="remove-profile-pic" class="remove-profile-picture-btn"
                                            onclick="removeProfilePicture()">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group textarea-style textarea-wrapper">
                            <label for="about">About</label>
                            <textarea id="about" name="about" maxlength="255" rows="4"
                                placeholder="Please provide a brief introduction about yourself."><?php if (get_user_data($user_id)['about'] !== null && get_user_data($user_id)['about'] !== "") {
                                    echo get_user_data($user_id)['about'];
                                }
                                ?></textarea>
                            <span id="charCount">0 / 255 characters used</span>
                        </div>
                        <button type="submit" name="submit" class="button-style">Submit</button>
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
                    <div class="div-space-top"></div>
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

                <div class="user-about-project">
                    <div class="about-section">
                        <div class="heading-content">
                            <div class="heading-style">
                                <p>About</p>
                            </div>
                        </div>
                        <div class="bottom-line"></div>
                        <div class="div-space-top"></div>
                        <div class="about">
                            <p>
                                <?php if (get_user_data($user_id)['about'] !== null && get_user_data($user_id)['about'] !== "") {
                                    echo get_user_data($user_id)['about'];
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="div-space-top"></div>
                    <div class="project-section">
                        <div class="user-project">
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
                                            echo '<div class="project-lst-name style="display: inline-block;">';
                                            echo '<a href="project.php?project_id=' . $project_id . '" class="project-link">';
                                            echo '    <div class="square" style="background-color:' . $background_color . '"></div>';
                                            echo '    <p class="project-title">' . add_ellipsis($project_name, 25) . '</p>';
                                            echo '</a>';
                                            echo '</div>';
                                            echo '<div class="project-options">';
                                            echo '<button onclick="editproject_popup_toggle(' . $project_id . ')">Edit</button>';
                                            echo '<a style="margin-left: 5px;" href="partial/delete_project.php?project_id=' . $project_id . '"><button>Delete</button></a>';
                                            echo '</div>';
                                            echo '</div>';
                                        }

                                    } else {
                                        // If no projects are assigned, display a message or do something else
                                        echo '<p>No projects assigned to this user.</p>';
                                    }
                                }
                                ?>
                                <div class="editproject-popup popup-style" id="editproject-popup">
                                    <div class="editproject-popup-content">
                                        <div class="heading-content">
                                            <span class="editproject-popup-close"
                                                onclick="editproject_popup_toggle()">&times;</span>
                                            <p class="heading-style">Edit Project</p>
                                        </div>
                                        <div class="bottom-line"></div>
                                        <div class="div-space-top"></div>
                                        <form action="partial/edit_project.php" method="post"
                                            enctype="multipart/form-data">
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
                                            <button type="submit" name="submit"
                                                class="editproject-submit-btn">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    function loadImgFile(event) {
        const imgPreview = document.getElementById("prof-pic");
        const fileInput = event.target;

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                imgPreview.src = e.target.result;
            };

            reader.readAsDataURL(fileInput.files[0]);
        }
    }
</script>
<script>
    function removeProfilePicture() {
        const confirmation = confirm("Are you sure you want to remove your profile picture?");

        if (confirmation) {
            fetch('partial/profile_partial/remove_profile_picture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user_id: <?php echo $user_id; ?> }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status == 'success') {
                        console.log(data.message);
                        displayPopupMessage(data.message, 'success');
                        location.reload();
                    } else if (data.status === 'error') {
                        displayPopupMessage(data.message, 'error');
                    }
                })
                .catch(error => console.error('Error removing profile picture:', error));
        }
    }
</script>
<script>
    const textArea = document.getElementById('about');
    const charCount = document.getElementById('charCount');

    textArea.addEventListener('focus', function () {
        charCount.style.display = 'block'; // Show the character count when the textarea is focused
    });

    textArea.addEventListener('blur', function () {
        charCount.style.display = 'none'; // Hide the character count when the textarea loses focus
    });

    textArea.addEventListener('input', function () {
        const currentText = textArea.value;
        const currentLength = currentText.length;
        charCount.textContent = `${currentLength} / 255 characters used`;
    });

    // Initialize the character count based on the initial value
    const initialText = textArea.value;
    const initialLength = initialText.length;
    charCount.textContent = `${initialLength} / 255 characters used`;
</script>