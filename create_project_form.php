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
        <div class="heading-content">
            <div class="heading-style">
                <p>Create a New Project</p>
            </div>
        </div>

        <form id="projectForm">
            <div class="div-space-top"></div>
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
                <input class="input-style" type="text" id="projectStartDate" name="start_date"
                    onfocus="this.type='date'" onblur="if(!this.value) { this.type='text'; }" placeholder="Start Date">
                <br>
                <span class="error-message" id="startDateError"></span>
            </div>

            <div class="div-space-top"></div>
            <div class="form-group">
                <input class="input-style" type="text" id="projectEndDate" name="end_date" onfocus="(this.type='date')"
                    onblur="if(!this.value)this.type='text';" placeholder="End Date">
                <br>
                <span class="error-message" id="endDateError"></span>
            </div>

            <div class="div-space-top"></div>
            <div class="form-group">
                <select class="input-style" name="priority" id="priority">
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
                <input type="submit" class="button-style" value="Submit">
            </div>

        </form>
    </div>
</div>


<script>
    // Call the function to initialize the date range picker
    $(document).ready(function () {
        initializeDateRangePicker('#projectStartDate', '#projectEndDate');
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

            if ($("#projectStartDate").val() === "") {
                $("#startDateError").text("Start Date is required.");
                isValid = false;
            }

            if ($("#projectEndDate").val() === "") {
                $("#endDateError").text("End Date is required.");
                isValid = false;
            }

            if ($("#priority").val() === "" || $("#priority").val() === null) {
                $("#priorityError").text("Priority is required.");
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            // Serialize the form data
            var formData = $(this).serialize();
            console.log(formData);

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
                                projectLink += '    <p class="project-title">' + truncateText(project.name, 23) + '</p>';
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