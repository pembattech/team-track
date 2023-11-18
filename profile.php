<?php include 'access_denied.php'; ?>

<title>Profile - TeamTrack</title>

<?php include 'partial/navbar.php'; ?>
<style>

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
            <div class="editprofile-popup popup-style " id="editprofile-popup">
                <div class="editprofile-popup-content">
                    <div class="heading-content">
                        <span class="editprofile-popup-close" onclick="editprofile_popup_toggle()">&times;</span>
                        <p class="heading-style">Edit Profile</p>
                    </div>
                    <div class="bottom-line"></div>
                    <div class="div-space-top"></div>
                    <form id="updateProfileForm" enctype="multipart/form-data">
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
                                        <label class="change-pic-lbl" for="new-profilepic">Change Profile
                                            Picture</label>

                                        <input class="d-none" id="new-profilepic" name="new-profilepic" type="file"
                                            onchange="loadImgFile(event)" />
                                        <span id="profilepic-error" class="error-message"></span>
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
                                } else {
                                    ?> <span style="color: var(--color-text-weak);">No about yet!</span>
                                    <?php
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

                                            // Output each project listing using HTML templates
                                            echo '<div class="project-lst" style="margin-bottom:8px;" data-project-id="' . $project_id . '">';
                                            echo '<div class="project-lst-name" style="display: inline-block;">';
                                            echo '<a href="project.php?project_id=' . $project_id . '" class="project-link">';
                                            echo '<div class="square" style="background-color:' . $background_color . '"></div>';
                                            echo '<p class="project-title">' . add_ellipsis($project_name, 20) . '</p>';
                                            echo '<span class="project_status" style="font-size: 12px; font-weight="300px";>&nbsp;(' . get_project_data($project_id)['status'] . ')</span>';
                                            echo '</a>';
                                            echo '</div>';
                                            if (get_project_owner_id($project_id) == $_SESSION['user_id']) {
                                                echo '<div class="project-options">';
                                                echo '<button class="edit-project-btn" id="edit-project-btn" data-project-id="' . $project_id . '">Edit</button>';
                                                echo '<button class="delete-project-btn" id="delete-project-btn" data-project-id="' . $project_id . '">Delete</button>';
                                                echo '</div>';
                                            }
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
                                                onclick="editproject_popup_toggle()">&times;
                                            </span>
                                            <p class="heading-style">Edit Project</p>
                                        </div>
                                        <div class="bottom-line"></div>
                                        <div class="div-space-top"></div>
                                        <form id="editProjectForm" enctype="multipart/form-data">
                                            <input type="hidden" name="project_id" id="project_id" value="">
                                            <div class="form-group">
                                                <input class="input-style" type="text" name="project_name"
                                                    id="project_name">
                                                <span id="projectname-error" class="error-message"></span>
                                            </div>
                                            <div class="form-group textarea-style textarea-wrapper">
                                                <textarea id="description" name="description" maxlength="255"
                                                    rows="4"></textarea>
                                                <span id="charCount">0 / 255 characters used</span>
                                                <span id="projectdescription-error" class="error-message"></span>
                                            </div>
                                            <div class="form-group">
                                                <input class="input-style" type="text" id="projectStartDate"
                                                    name="start_date" onfocus="this.type='date'"
                                                    onblur="if(!this.value) { this.type='text'; }"
                                                    placeholder="Start Date">
                                                <span id="projectStartDate-error" class="error-message"></span>
                                            </div>

                                            <div class="form-group">
                                                <input class="input-style" type="text" id="projectEndDate"
                                                    name="end_date" onfocus="(this.type='date')"
                                                    onblur="if(!this.value)this.type='text';" placeholder="End Date">
                                                <span id="projectEndDate-error" class="error-message"></span>
                                            </div>

                                            <div class="form-group">
                                                <?php include 'partial/project_partial/selectproject_priority.php';
                                                projectPrioritySelect($project_id);
                                                ?>
                                                <span id="projectPriority-error" class="error-message"></span>
                                            </div>

                                            <div class="form-group">
                                                <?php include 'partial/project_partial/selectproject_status.php';
                                                projectStatusSelect($project_id);
                                                ?>
                                                <span id="projectStatus-error" class="error-message"></span>
                                            </div>


                                            <button type="submit" id="submitEditProject"
                                                class="button-style">Submit</button>
                                        </form>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="deleteproject-popup popup-style" id="deleteproject-popup">
                        <div class="deleteproject-popup-content">
                            <div class="heading-content">
                                <span class="deleteproject-popup-close" onclick="deleteproject_popup_toggle()">&times;
                                </span>
                                <p class="heading-style">Delete Project</p>
                            </div>
                            <div class="bottom-line"></div>
                            <div class="div-space-top"></div>

                            <p style="color: var(--danger-color); font-weight: 900;">Are you sure to
                                delete project name?</p>
                            <button class="button-style" id="confirmDeleteProject">Yes</button>
                            <button class="button-style" id="cancelDeleteProject">Cancel</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    function editprofile_popup_toggle() {
        const popup = document.getElementById('editprofile-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }
</script>
<script>

    // Call the function to initialize the date range picker
    $(document).ready(function () {
        initializeDateRangePicker('#projectStartDate', '#projectEndDate');
    });

    // Event handler for clicking the "Edit" button
    $(document).on("click", ".edit-project-btn", function () {
        console.log("edit button clicked!");
        var projectId = $(this).data("project-id");

        editproject_popup_toggle(projectId)
    });

    function projectEditFormValidation() {
        // Clear previous error messages
        $('.error-message').text('');

        // Perform validation for each input field
        const projectName = $('#project_name').val();
        const projectDescription = $('#description').val();
        const startDate = $('#projectStartDate').val();
        const endDate = $('#projectEndDate').val();
        const priority = $('#project_priority').val();
        const status = $('#project_status').val();


        // Add your validation rules here
        if (projectName.trim() === '') {
            $("#projectname-error").text("Project name is required.");
            return false;
        }

        if (projectDescription.trim() === '') {
            $("#projectdescription-error").text("Project description is required.");
            return false;
        }

        if (startDate === '') {
            $("#projectStartDate-error").text("Project start date is required.");
            return false;
        }

        if (endDate === '') {
            $("#projectEndDate-error").text("Project end date is required.");
            return false;
        }

        if (priority === null || priority == 0) {
            $("#projectPriority-error").text("Project priority is required.");
            return false;
        }

        if (status === null || status == 0) {
            $("#projectStatus-error").text("Project status is required.");
            return false;
        }

        return true; // All validation passed
    }


    $('#editProjectForm').on("submit", function (event) {
        event.preventDefault();

        // Perform validation before submitting
        if (!projectEditFormValidation()) {
            return; // Stop form submission if validation fails
        }

        // Get the form data
        const formData = $(this).serialize();

        // Parse the formData string into an object
        const formDataObject = {};
        formData.split('&').forEach(function (pair) {
            const keyValue = pair.split('=');
            formDataObject[keyValue[0]] = decodeURIComponent(keyValue[1] || '');
        });


        // Use AJAX to fetch and display the edit form content
        $.ajax({
            url: "partial/project_partial/edit_project.php",
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status == 'success') {
                    console.log(response.message);
                    displayPopupMessage(response.message, 'success');

                    var latest_project_name = addEllipsis(response.latest_project_details.project_name, 20)
                    $('.project-lst[data-project-id="' + response.latest_project_details.project_id + '"] p').text(latest_project_name);

                    $('.project-lst[data-project-id="' + response.latest_project_details.project_id + '"] .project_status').text('\xa0' + "(" + response.latest_project_details.status + ")");

                } else if (response.status === 'error') {
                    displayPopupMessage(response.message, 'error');
                }

                // Get the project_id from formDataObject
                const project_id = formDataObject['project_id'];

                // Closing the editproject popup
                editproject_popup_toggle(project_id);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching edit form:", error);
            }
        });
    });

    // Event handler for clicking the "Delete" button
    $(document).on("click", "#delete-project-btn", function () {
        var deleteButton = $(this); // Store a reference to the button that was clicked
        var projectId = deleteButton.data("project-id");

        deleteproject_popup_toggle();

        // Pass projectId and deleteButton to deleteproject_popup_options
        deleteproject_popup_options(projectId, deleteButton);
    });

    function deleteproject_popup_toggle() {
        const popup = document.getElementById('deleteproject-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
    }

    function deleteproject_popup_options(projectId, deleteButton) {

        // Unbind any previously bound click event for #confirmDeleteProject
        $("#confirmDeleteProject").off("click");

        // Click event for confirming project deletion
        $("#confirmDeleteProject").on("click", function (e) {
            e.preventDefault();

            // Send an AJAX request to delete the project
            $.ajax({
                type: "POST",
                url: "partial/project_partial/delete_project.php",
                data: { project_id: projectId },
                success: function (response) {
                    // Close the popup and perform any additional actions
                    deleteproject_popup_toggle();

                    // Handle the response from the server
                    if (response.status == 'success') {
                        // Optionally, remove the deleted project listing from the DOM
                        deleteButton.closest(".project-lst").remove();

                        displayPopupMessage(response.message, 'success');
                    } else if (response.status === 'error') {
                        displayPopupMessage(response.message, 'error');
                    }
                },
                error: function () {
                    // Handle the AJAX error
                    console.log("An error occurred while deleting the project.");
                }
            });
        });
    }

    // Click event for canceling project deletion
    $("#cancelDeleteProject").click(function (e) {
        e.preventDefault();
        deleteproject_popup_toggle(); // Close the popup
    });




</script>
<script>
    function editproject_popup_toggle(projectId) {
        const popup = document.getElementById('editproject-popup');
        popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';

        if (popup.style.display === 'block') {
            // If the popup is being displayed, set the value of the hidden input field to the project ID
            document.getElementById('project_id').value = projectId;

            initializeCharacterCount();

            // Fetch project data using AJAX
            fetchProjectData(projectId);
        }
    }

    function fetchProjectData(projectId) {
        $.ajax({
            url: 'partial/fetch_project_data.php',
            type: 'GET',
            data: { project_id: projectId },
            dataType: 'json',
            success: function (data) {
                // Populate the description field with the fetched data
                $('#project_id').val(data.project_id);
                $('#project_name').val(data.project_name);
                $('#description').val(data.description);
                $('#projectStartDate').val(data.start_date);
                $('#projectEndDate').val(data.end_date);
                $('#project_priority').val(data.priority);
                $('#project_status').val(data.status);

            },
            error: function (xhr, status, error) {
                console.error('Error fetching project data:', error);
            }
        });
    }

</script>
<script>
    function loadImgFile(event) {

        validateProfilePicture()

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
    function validateProfilePicture() {
        const fileInput = document.getElementById("new-profilepic");
        const errorSpan = document.getElementById("profilepic-error");

        // Check if a file is selected
        if (!fileInput.files || fileInput.files.length === 0) {
            errorSpan.textContent = "Please select a profile picture.";
            return false;
        }

        const allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes

        const selectedFile = fileInput.files[0];
        const fileName = selectedFile.name;
        const fileExtension = fileName.split(".").pop().toLowerCase();

        // Check file extension
        if (!allowedExtensions.includes(fileExtension)) {
            errorSpan.textContent = "Invalid file format. Please use jpg, jpeg, png, or gif.";
            return false;
        }

        // Check file size
        if (selectedFile.size > maxFileSize) {
            errorSpan.textContent = "File size exceeds the maximum allowed (5MB).";
            return false;
        }

        // Clear any previous error messages
        errorSpan.textContent = "";

        // Upload profile picture using AJAX
        uploadProfilePicture(selectedFile);

        // Prevent the form from submitting synchronously
        return false;
    }

    function uploadProfilePicture(file) {
        const formData = new FormData();
        formData.append("new-profilepic", file);

        $.ajax({
            url: "partial/upload_profile_picture.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    // Update the displayed profile picture using jQuery
                    $(".profpic").attr("src", response.newProfilePicture);

                    // Close the edit profile popup
                    editprofile_popup_toggle();

                    location.reload();

                } else if (response.status === "error") {
                    // Handle error, e.g., display an error message
                    console.error(response.message);

                    document.getElementById("profilepic-error").textContent = response.message;
                }
            },
            error: function (xhr, status, error) {
                // Handle AJAX error
                console.error("AJAX Error:", error);
            }
        });
    }

    function removeProfilePicture() {
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
</script>
<script>
    // Function to update the "About" section via AJAX
    function updateAbout(aboutText) {
        $.ajax({
            url: 'partial/update_profile.php',
            type: 'POST',
            data: { about: aboutText },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    // Update the "About" section on the page
                    $('#about').val(aboutText);

                    // Update the content in the <p> element
                    if (aboutText.length === 0) {
                        $('.about-section .about p').html('<span style="color: var(--color-text-weak);">No about yet!</span>');
                    } else {
                        $('.about-section .about p').text(aboutText);
                    }

                    // Close the edit profile popup
                    editprofile_popup_toggle();

                    // Display a success message or perform other actions as needed
                    console.log('About section updated successfully.');

                    displayPopupMessage(response.message, 'success');
                } else if (response.status === 'error') {
                    // Handle errors or display error messages
                    console.error('Error updating the About section.');
                    displayPopupMessage(response.message, 'error');
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                // Handle AJAX errors
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });
    }

    // Listen for a change event on the "About" textarea
    $('#about').on('change', function () {
        // Get the updated "About" text
        var updatedAboutText = $(this).val();
        // Update the "About" section via AJAX
        updateAbout(updatedAboutText);
    });

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
<script>
    function initializeCharacterCount() {
        const textArea1 = document.getElementById('description');
        console.log(textArea1);
        const charCount1 = document.getElementById('charCount');
        console.log(charCount1);

        textArea1.addEventListener('focus', function () {
            charCount1.style.display = 'block'; // Show the character count when the textarea is focused
        });

        textArea1.addEventListener('blur', function () {
            charCount1.style.display = 'none'; // Hide the character count when the textarea loses focus
        });

        textArea1.addEventListener('input', function () {
            const currentText1 = textArea1.value;
            const currentLength1 = currentText1.length;
            charCount1.textContent = `${currentLength1} / 255 characters used`;
        });

        // Initialize the character count based on the initial value
        const initialText1 = textArea1.value;
        const initialLength1 = initialText1.length;
        charCount1.textContent = `${initialLength1} / 255 characters used`;
    }
</script>