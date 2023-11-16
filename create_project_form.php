<?php include 'access_denied.php'; ?>

<title>Create Project - TeamTrack</title>
<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>

    <?php
    function add_ellipsis_($string, $aftercount)
    {
        if (strlen($string) > $aftercount) {
            $string = substr($string, 0, $aftercount) . "...";
        }
        return $string;
    }
    ?>

    <div class="main-content">
        <div class="project-container">
            <div class="left-container">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>Create a New Project</p>
                    </div>
                </div>
                <div class="project_create_form">
                    <form id="projectForm">
                        <div class="form-group">
                            <input type="text" class="input-style" name="project_name" id="projectname"
                                placeholder="Name your Project">
                            <br>
                            <span class="error-message" id="nameError"></span>
                        </div>

                        <div class="div-space-top"></div>
                        <div class="form-group">
                            <div class="textarea-style textarea-wrapper">
                                <textarea name="description" maxlength="255" id="projectdesc" rows="4"
                                    placeholder="Describe here"></textarea>
                                <span id="charCount">0 / 255 characters used</span>
                            </div>
                            <span class="error-message" id="descError"></span>
                        </div>

                        <div class="div-space-top"></div>
                        <div class="form-group">
                            <input class="input-style" type="text" id="createprojectStartDate" name="start_date"
                                onfocus="this.type='date'" onblur="if(!this.value) { this.type='text'; }"
                                placeholder="Start Date">
                            <br>
                            <span class="error-message" id="startDateError"></span>
                        </div>

                        <div class="div-space-top"></div>
                        <div class="form-group">
                            <input class="input-style" type="text" id="createprojectEndDate" name="end_date"
                                onfocus="(this.type='date')" onblur="if(!this.value)this.type='text';"
                                placeholder="End Date">
                            <br>
                            <span class="error-message" id="endDateError"></span>
                        </div>

                        <div class="div-space-top"></div>
                        <div class="form-group">
                            <select class="input-style" name="priority" id="createpriority">
                                <option value="0" selected hidden>Select priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <br>
                            <span class="error-message" id="priorityError"></span>
                        </div>

                        <div class="div-space-top"></div>
                        <div class="form-group">
                            <select class="input-style" name="status" id="createstatus">
                                <option value="0" selected hidden>Select status</option>
                                <option value="active">Active</option>
                                <option value="canceled">Canceled</option>
                                <option value="complete">Complete</option>
                            </select>
                            <br>
                            <span class="error-message" id="statusError"></span>
                        </div>

                        <div class="div-space-top"></div>
                        <div class="form-group">
                            <input type="submit" class="button-style" value="Submit">
                        </div>

                    </form>
                </div>
            </div>
            <div class="right-container">
                <div class="heading-content">
                    <div class="heading-style">
                        <p>List of Created Project</p>
                    </div>
                </div>

                <div class="project-section">
                    <div class="user-project">
                        <div class="project" id="created_projects">
                            <!-- display list of created project dynamically -->
                        </div>

                        <div class="editproject-popup popup-style" id="editproject-popup">
                            <div class="editproject-popup-content">
                                <div class="heading-content">
                                    <span class="editproject-popup-close" onclick="editproject_popup_toggle()">&times;
                                    </span>
                                    <p class="heading-style">Edit Project</p>
                                </div>
                                <div class="bottom-line"></div>
                                <div class="div-space-top"></div>
                                <form id="editProjectForm" enctype="multipart/form-data">
                                    <input type="hidden" name="project_id" id="project_id" value="">
                                    <div class="form-group">
                                        <input class="input-style" type="text" name="project_name" id="project_name">
                                        <span id="projectname-error" class="error-message"></span>
                                    </div>
                                    <div class="form-group textarea-style textarea-wrapper">
                                        <textarea id="description" name="description" maxlength="255"
                                            rows="4"></textarea>
                                        <span id="charCount">0 / 255 characters used</span>
                                        <span id="projectdescription-error" class="error-message"></span>
                                    </div>
                                    <div class="form-group">
                                        <input class="input-style" type="text" id="projectStartDate" name="start_date"
                                            onfocus="this.type='date'" onblur="if(!this.value) { this.type='text'; }"
                                            placeholder="Start Date">
                                        <span id="projectStartDate-error" class="error-message"></span>
                                    </div>

                                    <div class="form-group">
                                        <input class="input-style" type="text" id="projectEndDate" name="end_date"
                                            onfocus="(this.type='date')" onblur="if(!this.value)this.type='text';"
                                            placeholder="End Date">
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

                                    <button type="submit" id="submitEditProject" class="button-style">Submit</button>
                                </form>
                            </div>
                        </div>
                        <div class="deleteproject-popup popup-style" id="deleteproject-popup">
                            <div class="deleteproject-popup-content">
                                <div class="heading-content">
                                    <span class="deleteproject-popup-close"
                                        onclick="deleteproject_popup_toggle()">&times;
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


    <script>
        $(document).ready(function () {
            // Call the function to initialize the date range picker
            initializeDateRangePicker('#createprojectStartDate', '#createprojectEndDate');

            // Call the function to populate the project list initially
            updateProjectList();
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#projectForm").submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                // Clear previous error messages
                $(".error-message").text("");

                // Validate inputs
                var isValid = true;

                if ($("#projectname").val() === "") {
                    $("#nameError").text("Project Name is required.");
                    isValid = false;
                }

                if ($("#projectdesc").val() === "") {
                    $("#descError").text("Description is required.");
                    isValid = false;
                }

                if ($("#createprojectStartDate").val() === "") {
                    $("#startDateError").text("Start Date is required.");
                    isValid = false;
                }

                if ($("#createprojectEndDate").val() === "") {
                    $("#endDateError").text("End Date is required.");
                    isValid = false;
                }

                if ($("#createpriority").val() === "" || $("#createpriority").val() == null || $("#createpriority").val() == "0") {
                    $("#priorityError").text("Priority is required.");
                    isValid = false;
                }

                if ($("#createstatus").val() === "" || $("#createstatus").val() == null || $("#createstatus").val() == "0") {
                    $("#statusError").text("Status is required.");
                    isValid = false;
                }

                if (!isValid) {
                    return;
                }

                // Serialize the form data
                var formData = $(this).serialize();

                // Send AJAX request
                $.ajax({
                    type: "POST",
                    url: "partial/project_partial/create_project.php",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'success') {
                            console.log(response.message);
                            displayPopupMessage(response.message, 'success');
                        } else if (response.status === 'error') {
                            displayPopupMessage(response.message, 'error');
                        }

                        // Clear the form inputs on success
                        $("#projectForm")[0].reset();

                        // After successfully creating the project, update the project list
                        $.ajax({
                            type: "GET", // Use GET to fetch the updated list
                            url: "partial/project_partial/fetch_projects.php", // URL to fetch the updated project list
                            dataType: "json",
                            success: function (projects) {
                                // Call the function to populate the project list on the right side of create project form
                                updateProjectList();

                                // Clear the existing project list
                                $("#projectListContainer").empty();

                                // JavaScript function to truncate text
                                function truncateText(text, maxLength) {
                                    if (text.length > maxLength) {
                                        return text.substring(0, maxLength) + '...';
                                    }
                                    return text;
                                }

                                // Inside your success callback for fetching projects
                                $.each(projects, function (index, project) {
                                    var projectLink = '<div class="project-lst">';
                                    projectLink += '<a href="project.php?project_id=' + project.id + '" class="project-link" id="link">';
                                    projectLink += '    <div class="square" style="background-color:' + project.color + '"></div>';
                                    projectLink += '    <p class="project-title">' + truncateText(project.name, 20) + '</p>';
                                    projectLink += '</a>';
                                    projectLink += '</div>';
                                    $("#projectListContainer").append(projectLink);
                                });

                            },
                            error: function () {
                                console.log("An error occurred while fetching the updated project list.");
                            }
                        });
                    },
                    error: function () {
                        // $("#responseMessage").html("An error occurred while submitting the form.");
                        console.log("An error occurred while submitting the form.");
                    }
                });
            });
        });

    </script>
    <script>
        const textArea = document.getElementById('projectdesc');
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
        // Call the function to initialize the date range picker
        $(document).ready(function () {
            initializeDateRangePicker('#projectStartDate', '#projectEndDate');
        });

        // Event handler for clicking the "Edit" button
        $(document).on("click", ".edit-project-btn", function () {
            console.log("edit button clicked!");
            var projectId = $(this).data("project-id");
            console.log(projectId);

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

        // Submit the edited task details when the form is submitted
        // $(document).on("click", ".edit-project-btn", function () {

        $('#editProjectForm').on("submit", function (event) {
            event.preventDefault();

            // Perform validation before submitting
            if (!projectEditFormValidation()) {
                return; // Stop form submission if validation fails
            }

            // Get the form data
            const formData = $(this).serialize();
            console.log(formData);

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
                    } else if (response.status === 'error') {
                        displayPopupMessage(response.message, 'error');
                    }

                    // Get the project_id from formDataObject
                    const project_id = formDataObject['project_id'];

                    // After successfully creating the project, update the project list
                    $.ajax({
                        type: "GET", // Use GET to fetch the updated list
                        url: "partial/project_partial/fetch_projects.php", // URL to fetch the updated project list
                        dataType: "json",
                        success: function (projects) {
                            // Call the function to populate the project list on the right side of create project form
                            updateProjectList();

                            // Clear the existing project list
                            $("#projectListContainer").empty();

                            // JavaScript function to truncate text
                            function truncateText(text, maxLength) {
                                if (text.length > maxLength) {
                                    return text.substring(0, maxLength) + '...';
                                }
                                return text;
                            }

                            // Inside your success callback for fetching projects
                            $.each(projects, function (index, project) {
                                var projectLink = '<div class="project-lst">';
                                projectLink += '<a href="project.php?project_id=' + project.id + '" class="project-link" id="link">';
                                projectLink += '    <div class="square" style="background-color:' + project.color + '"></div>';
                                projectLink += '    <p class="project-title">' + truncateText(project.name, 20) + '</p>';
                                projectLink += '</a>';
                                projectLink += '</div>';
                                $("#projectListContainer").append(projectLink);
                            });

                        },
                        error: function () {
                            console.log("An error occurred while fetching the updated project list.");
                        }
                    });

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

                        // After successfully creating the project, update the project list
                        $.ajax({
                            type: "GET", // Use GET to fetch the updated list
                            url: "partial/project_partial/fetch_projects.php", // URL to fetch the updated project list
                            dataType: "json",
                            success: function (projects) {
                                // Call the function to populate the project list on the right side of create project form
                                updateProjectList();

                                // Clear the existing project list
                                $("#projectListContainer").empty();

                                // Inside your success callback for fetching projects
                                $.each(projects, function (index, project) {
                                    var projectLink = '<div class="project-lst">';
                                    projectLink += '<a href="project.php?project_id=' + project.id + '" class="project-link" id="link">';
                                    projectLink += '    <div class="square" style="background-color:' + project.color + '"></div>';
                                    projectLink += '    <p class="project-title">' + addEllipsis(project.name, 20) + '</p>';
                                    projectLink += '</a>';
                                    projectLink += '</div>';
                                    $("#projectListContainer").append(projectLink);
                                });

                            },
                            error: function () {
                                console.log("An error occurred while fetching the updated project list.");
                            }
                        });
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
        function updateProjectList() {
            // Send an AJAX request to fetch the updated project list
            $.ajax({
                type: "GET",
                url: "partial/project_partial/fetch_created_projects_list.php",
                dataType: "html",
                success: function (html) {
                    console.log(html)
                    // Replace the content of the project list container with the updated list
                    $("#created_projects").html(html);
                },
                error: function () {
                    console.log("An error occurred while fetching the updated project list.");
                }
            });
        }
    </script>