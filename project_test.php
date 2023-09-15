<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Sortable Tasks Table with Collapsible Sections</title>
    <!-- Add jQuery UI library -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .task-name-cell::before {
            content: '\2713';
            /* Checkmark Unicode character */
            color: green;
            /* Color of the checkmark */
            margin-right: 5px;
            /* Spacing between checkmark and text */
        }


        /* Add this CSS to your existing styles */
        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #fff;
            color: #000;
            text-align: left;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 100%;
            /* Position the tooltip above the cell */
            left: 0;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx */
        .task-popup {
            position: fixed;
            top: 0;
            right: -440px;
            /* Offscreen position initially */
            width: 400px;
            height: 100%;
            background-color: red;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: right 0.3s ease-in-out;
        }

        /* Show the slide-in popup */
        .task-popup.active {
            right: 0;
        }

        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            cursor: pointer;
        }

        .collapsible {
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .collapsible h2 {
            margin: 0;
            padding: 10px;
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .collapsible h2.active {
            background-color: #ddd;
        }

        .collapsible table {
            display: none;
        }

        .collapsible table.show {
            display: table;
        }
    </style>
</head>

<body>
    <?php
    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "teamtrack";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Check if the 'project_id' parameter is present in the URL
    if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
        $project_id = $_GET['project_id'];


        // Query to fetch project details from the 'Projects' table
        $sql_project = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result_project = mysqli_query($connection, $sql_project);

        // Query to fetch project owner's details from the 'Users' table
        $sql_owner = "SELECT Users.*, ProjectUsers.is_projectowner, ProjectUsers.user_role FROM Users INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id WHERE ProjectUsers.project_id = $project_id AND ProjectUsers.is_projectowner = 1";
        $result_owner = mysqli_query($connection, $sql_owner);
        $project_owner = mysqli_fetch_assoc($result_owner);

        // Query to fetch other users associated with the project from the 'ProjectUsers' table (excluding the owner)
        $sql_users = "SELECT Users.user_id, Users.username, ProjectUsers.is_projectowner, ProjectUsers.user_role
        FROM Users
        JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
        WHERE ProjectUsers.project_id = $project_id
        ORDER BY ProjectUsers.is_projectowner DESC, Users.username";
        $result_users = mysqli_query($connection, $sql_users);

        // Query to get the total number of tasks for the project
        $sql_total_tasks = "SELECT COUNT(*) AS total_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id)";
        $result_total_tasks = mysqli_query($connection, $sql_total_tasks);
        $row_total_tasks = mysqli_fetch_assoc($result_total_tasks);
        $total_tasks = $row_total_tasks['total_tasks'];

        // Query to get the number of completed tasks for the project
        $sql_completed_tasks = "SELECT COUNT(*) AS completed_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id) AND status = 'completed'";
        $result_completed_tasks = mysqli_query($connection, $sql_completed_tasks);
        $row_completed_tasks = mysqli_fetch_assoc($result_completed_tasks);
        $completed_tasks = $row_completed_tasks['completed_tasks'];

        // Query to get the number of incomplete tasks for the project
        $sql_incomplete_tasks = "SELECT COUNT(*) AS incomplete_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id) AND status != 'completed'";
        $result_incomplete_tasks = mysqli_query($connection, $sql_incomplete_tasks);
        $row_incomplete_tasks = mysqli_fetch_assoc($result_incomplete_tasks);
        $incomplete_tasks = $row_incomplete_tasks['incomplete_tasks'];

        // Query to get the number of overdue tasks for the project
        $current_date = date('Y-m-d');
        $sql_overdue_tasks = "SELECT COUNT(*) AS overdue_tasks FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id) AND status != 'completed' AND end_date < '$current_date'";
        $result_overdue_tasks = mysqli_query($connection, $sql_overdue_tasks);
        $row_overdue_tasks = mysqli_fetch_assoc($result_overdue_tasks);
        $overdue_tasks = $row_overdue_tasks['overdue_tasks'];

        // Prepare and execute the SQL query using a prepared statement
        $stmt = $connection->prepare("SELECT * FROM Tasks WHERE projectuser_id IN (SELECT projectuser_id FROM ProjectUsers WHERE project_id = ?)");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Store tasks grouped by section
        $tasksBySection = array(
            "To Do" => array(),
            "Doing" => array(),
            "Done" => array()
        );

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $section = $row['section'];
                if (!isset($tasksBySection[$section])) {
                    $tasksBySection[$section] = array();
                }
                $tasksBySection[$section][] = $row;
            }
        }

        $stmt->close();
    }

    // Function to format a date range
    function formatDateRange1($startDate, $endDate)
    {
        if (!empty($startDate) && !empty($endDate)) {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            $startYear = $start->format('Y');
            $endYear = $end->format('Y');

            $formattedStart = $start->format('M j');
            $formattedEnd = $end->format('M j');

            if ($startYear === $endYear) {
                return "{$formattedStart} - {$formattedEnd}, {$startYear}";
            } else {
                return "{$formattedStart}, {$startYear} - {$formattedEnd}, {$endYear}";
            }
        }
        return "n/a";
    }
    ?>


    <div id="deletePopup" style="display: none;">
        <h3>Delete Tasks</h3>
        <p>Are you sure you want to delete the selected tasks?</p>
        <button id="confirmDelete">Delete</button>
        <button id="cancelDelete">Cancel</button>
    </div>

    <?php if (isset($tasksBySection)): ?>
        <?php if (!empty($tasksBySection)): ?>
            <?php foreach ($tasksBySection as $section => $tasks): ?>
                <div class="collapsible">
                    <h2>
                        <?php echo $section; ?>
                        <span class="section-task-count">(
                            <?php echo count($tasks); ?>)
                        </span>
                    </h2>
                    <table class="sortable show" data-section="<?php echo $section; ?>">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Task Name</th>
                                <th>Task Description</th>
                                <th>Assignee</th>
                                <th>Due Date</th> <!-- Updated header to "Due Date" -->
                                <th>Status</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr data-task-id="<?php echo $task['task_id']; ?>"
                                    class="<?php echo $task['status'] === 'Done' ? 'completed' : 'incomplete'; ?>">
                                    <td>
                                        <?php echo $task['task_name']; ?>
                                    </td>
                                    <td class="tooltip">
                                        <span>
                                            <?php echo $task['task_description']; ?>
                                        </span>
                                        <div class="tooltiptext">
                                            <?php echo $task['task_description']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $task['assignee']; ?>
                                    </td>
                                    <td>
                                        <span class="due-date">
                                            <?php echo formatDateRange1($task['start_date'], $task['end_date']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $task['status']; ?>
                                    </td>
                                    <td>
                                        <?php echo $task['priority']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tasks assigned to this project.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Slide-in popup to display task description -->
    <div class="task-popup" id="taskPopup">
        <div class="heading-content">
            <div class="heading-style">
                <p>Edit Task</p>
            </div>
            <div class="bottom-line"></div>
            <div class="div-space-top"></div>
            <button type="button" id="closeButton">Close</button>
            <button type="button" id="deleteButton">Delete Task</button>
            <div class="div-space-top"></div>
        </div>
        <div class="bottom-line"></div>
        <div class="div-space-top"></div>
        <form id="editTaskForm">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <input type="hidden" id="editTaskId" name="task_id">
            <label for="editTaskName">Task Name:</label>
            <input type="text" id="editTaskName" name="task_name" required>
            <br>

            <label for="editAssignee">Assignee:</label>
            <textarea id="editAssignee" name="assignee" required></textarea>
            <br>

            <label for="editTaskDescription">Task Description:</label>
            <textarea id="editTaskDescription" name="task_description" required></textarea>
            <br>

            <label for="editStartDate">Start Date:</label>
            <input type="date" id="editStartDate" name="start_date" required>
            <br>

            <label for="editEndDate">End Date:</label>
            <input type="date" id="editEndDate" name="end_date" required>
            <br>

            <label for="editStatus">Status:</label>
            <select id="editStatus" name="status" required>
                <option value="At risk">At risk</option>
                <option value="Off Track">Off track</option>
                <option value="On Track">On track</option>
                <option value="On Hold">On Hold</option>
                <option value="Cancelled">Cancelled</option>
                <option value="Blocked">Blocked</option>
                <option value="Waiting for Approval">Waiting for Approval</option>
                <option value="In Review">In Review</option>
            </select>
            <br>

            <label for="editPriority">Priority:</label>
            <select id="editPriority" name="priority" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            <br>

            <button type="submit" id="submitButton">Save Changes</button>
        </form>
    </div>

    <script src="static/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        // Call the fetchTasks function on page load
        $(document).ready(function () {
            fetchTasks();
        });

        // Call the function to initialize the date range picker
        $(document).ready(function () {
            initializeDateRangePicker('editStartDate', 'editEndDate');
        });

        // JavaScript for collapsible sections
        $(document).ready(function () {
            $('.collapsible h2').click(function () {
                $(this).toggleClass('active');
                $(this).next('table').toggleClass('show');
            });
        });

        // JavaScript for sorting between sections
        $(document).ready(function () {
            // Initialize sortable for each section table
            $('.sortable').sortable({
                connectWith: '.sortable', // Enable sorting between sections
                placeholder: 'ui-state-highlight', // Style for the placeholder during drag-and-drop
                items: 'tr', // Limit sorting to rows only within the current section
                scroll: true,
                scrollSensitivity: 80,
                scrollSpeed: 3,
                update: function (event, ui) {
                    // Get the dragged task's ID
                    const taskId = ui.item.attr('data-task-id');

                    // Get the destination section's ID
                    const sectionId = ui.item.closest('.collapsible').find('h2').text().trim();
                    console.log(sectionId);


                    // Update the task's section in the database using an AJAX request
                    $.ajax({
                        url: 'partial/task_partial/update_task_section.php', // Replace with the URL to your update task section PHP file
                        method: 'POST',
                        data: {
                            projectowner_id: <?php echo $project_owner['user_id']; ?>,
                            task_id: taskId,
                            section: sectionId
                        },
                        success: function (response) {
                            console.log(response);
                            // fetchTasks();
                            console.log('Task section updated successfully.');
                            if (response.status == 'success') {
                                console.log(response.message);
                                displayPopupMessage(response.message, 'success');
                            } else if (response.status === 'error') {
                                displayPopupMessage(response.message, 'error');
                            }

                            fetchTasks();
                        },
                        error: function (xhr, status, error) {
                            // Handle the error if needed
                            console.error('Error updating task section:', error);
                        }
                    });

                }
            });
        });

        // Variable to store the current task ID
        let currentTaskId;

        // JavaScript for handling task click and displaying popup
        $(document).ready(function () {
            // Check if the clicked element is not the "Save Changes" button
            function isNotSaveButton(target) {
                return !$(target).closest('#submitButton').length;
            }

            // Use event delegation for the click event on task rows
            $(document).on('click', '.sortable tr', function (event) {
                // Check if the clicked element is not the "Save Changes" button
                if (isNotSaveButton(event.target)) {
                    // Get the task details from the clicked row
                    const taskId = $(this).attr('data-task-id');
                    const taskName = $(this).find('td:nth-child(1)').text();
                    const taskDescription = $(this).find('td:nth-child(2)').text();
                    const assignee = $(this).find('td:nth-child(3)').text();
                    const startDate = $(this).find('td:nth-child(4) .due-date').text();
                    const endDate = $(this).find('td:nth-child(5) .due-date').text();
                    const status = $(this).find('td:nth-child(6)').text();
                    const priority = $(this).find('td:nth-child(7)').text();

                    // Remove the active class from all task rows
                    $('.sortable tr').removeClass('active-task');

                    // Add the active class to the clicked task row
                    $(this).addClass('active-task');

                    // Set the task details in the edit popup form
                    $('#editTaskId').val(taskId);
                    $('#editTaskName').val(taskName);
                    $('#editTaskDescription').val(taskDescription);
                    $('#editAssignee').val(assignee);
                    $('#editStartDate').val(startDate);
                    $('#editEndDate').val(endDate);
                    $('#editStatus').val(status);
                    $('#editPriority').val(priority);

                    // Store the task ID in the variable
                    currentTaskId = taskId;

                    if (taskName !== '') {
                        // Show the popup with animation
                        $('#taskPopup').addClass('active');
                        // Fetch task details and populate the edit form
                        fetchTaskDetails(taskId);
                    } else if (taskName === '') {
                        $('#taskPopup').removeClass('active');
                    }
                }
            });

            // Submit the edited task details when the form is submitted
            $('#editTaskForm').submit(function (event) {
                event.preventDefault();

                // Perform validation before submitting
                if (!updateFormValidation()) {
                    return; // Stop form submission if validation fails
                }

                // Get the form data
                const formData = $(this).serialize();
                console.log(formData);

                // Send an AJAX request to update the task details
                $.ajax({
                    url: 'partial/task_partial/update_task.php', // Replace with the URL to your update task PHP file
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.status == 'success') {
                            console.log(response.message);
                            displayPopupMessage(response.message, 'success');
                        } else if (response.status === 'error') {
                            displayPopupMessage(response.message, 'error');
                        }

                        // Hide the edit popup with animation
                        $('#taskPopup').removeClass('active');

                        fetchTasks();

                    },
                    error: function (xhr, status, error) {
                        // Handle the error if needed
                        console.error('Error updating task:', error);
                        console.log(xhr.responseText);
                    }
                });

            });

            function updateFormValidation() {
                // Clear previous error messages
                $('.error-message').text('');

                // Perform validation for each input field
                const taskName = $('#editTaskName').val();
                const assignee = $('#editAssignee').val();
                const taskDescription = $('#editTaskDescription').val();
                const startDate = $('#editStartDate').val();
                const endDate = $('#editEndDate').val();
                const status = $('#editStatus').val();
                const priority = $('#editPriority').val();

                console.log(taskName);
                console.log(assignee);
                console.log(taskDescription);
                console.log(startDate);
                console.log(endDate);
                console.log(status);
                console.log(priority);


                // Add your validation rules here
                if (taskName.trim() === '') {
                    $("#editTaskName-error").text("Task name is required.");
                    return false;
                }

                if (assignee === null || assignee == 0 || assignee == -1) {
                    $("#editAssignee-error").text("Task assignee is required.");
                    return false;
                }


                if (taskDescription.trim() === '') {
                    $("#editTaskDescription-error").text("Task description is required.");
                    return false;
                }

                if (startDate === '') {
                    $("#editStartDate-error").text("Task start date is required.");
                    return false;
                }

                if (endDate === '') {
                    $("#editEndDate-error").text("Task end date is required.");
                    return false;
                }

                if (status === null || status == 0) {
                    $("#editStatus-error").text("Task status is required.");
                    return false;
                }

                if (priority === null || priority == 0) {
                    $("#editPriority-error").text("Task priority is required.");
                    return false;
                }

                return true; // All validation passed
            }


            // Close the popup when the close button is clicked
            $('#closeButton').click(function () {
                // Hide the popup with animation
                $('#taskPopup').removeClass('active');

                // Remove the active class from all task rows
                $('.sortable tr').removeClass('active-task');

            });

            // Delete the task when the delete button is clicked
            $('#deleteButton').click(function () {
                // Get the task ID from the variable
                const taskId = currentTaskId;

                // Send an AJAX request to delete the task
                $.ajax({
                    url: 'partial/task_partial/delete_task.php', // Replace with the URL to your delete task PHP file
                    method: 'POST',
                    data: {
                        projectowner_id: <?php echo $project_owner['user_id']; ?>,
                        task_id: taskId
                    },
                    success: function (response) {
                        // Handle the response if needed
                        console.log('Task deleted successfully.');
                        // Hide the popup with animation
                        $('#taskPopup').removeClass('active');
                        // Remove the active class from all task rows
                        $('.sortable tr').removeClass('active-task');

                        // Fetch tasks again to update the list
                        fetchTasks();
                        if (response.status == 'success') {
                            console.log(response.message);
                            displayPopupMessage(response.message, 'success');
                        } else if (response.status === 'error') {
                            displayPopupMessage(response.message, 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle the error if needed
                        console.error('Error deleting task:', error);
                    }
                });
            });
        });

        // Function to fetch tasks from the server using AJAX
        function fetchTasks() {
            // Get the project_id from the URL
            const project_id = <?php echo isset($_GET['project_id']) ? $_GET['project_id'] : 'null'; ?>;
            if (project_id === null) {
                console.error('No project_id found in URL.');
                return;
            }

            // Send an AJAX request to fetch tasks for the given project_id
            $.ajax({
                url: 'partial/task_partial/fetch_tasks.php',
                method: 'GET',
                data: { project_id: project_id },
                success: function (response) {
                    console.log(response);
                    // Handle the response and update the task tables here
                    const tasksBySection = response.tasksBySection;
                    const project_id = response.project_id;
                    updateTaskTables(tasksBySection);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching tasks:', error);
                }
            });
        }

        // Function to update the task tables with fetched data
        function updateTaskTables(tasksBySection) {
            // Loop through each section and update the corresponding table
            for (const section in tasksBySection) {
                if (tasksBySection.hasOwnProperty(section)) {
                    // Get the corresponding table
                    const $table = $(`.sortable[data-section="${section}"]`);
                    const $tbody = $table.find('tbody');

                    // Clear the table body
                    $tbody.empty();

                    // Loop through tasks in the section and append rows to the table
                    tasksBySection[section].forEach(task => {
                        const $row = createTaskRow(task);
                        $tbody.append($row);
                    });
                }
            }
        }

        // Function to create a table row for a task
        function createTaskRow(task) {
            const $row = $('<tr>');
            $row.attr('data-task-id', task.task_id);
            const assigneeName = task.assignee_name || 'Not Assigned'; // Use 'Not Assigned' if assignee is empty
            const taskpriority = task.priority || 'n/a'; // Use 'n/a' if priority is empty
            $row.addClass(task.status === 'Done' ? 'completed' : 'incomplete');

            // Add a checkbox field for task selection
            const $checkboxCell = $('<td>');
            const $checkbox = $('<input>', {
                type: 'checkbox',
                class: 'task-checkbox',
                'data-task-id': task.task_id
            });
            $checkboxCell.append($checkbox);
            $row.append($checkboxCell);

            const taskName = task.task_name.length > 19 ? addTooltip(task.task_name) : task.task_name;
            $row.append(`<td class="task-name">${taskName}</td>`);
            $row.append(`<td class="task_desc tooltip"><span>${addEllipsis(task.task_description, 29)}</span><div class="tooltiptext">${task.task_description}</div></td>`);
            $row.append(`<td>${assigneeName}</td>`);
            $row.append(`<td><span class="due-date">${formatDateRange(task.start_date, task.end_date)}</span></td>`);
            $row.append(`<td>${task.status}</td>`);
            $row.append(`<td>${taskpriority}</td>`);

            return $row;
        }

        // Function to add a tooltip for long task names
        function addTooltip(taskName) {
            return `<span class="tooltip">${addEllipsis(taskName, 18)}<div class="tooltiptext">${taskName}</div></span>`;
        }

        // Array to store selected task IDs
        const selectedTasks = [];

        // Function to toggle task selection
        function toggleTaskSelection(taskId) {
            console.log(taskId);
            const index = selectedTasks.indexOf(taskId);
            console.log(index);
            if (index === -1) {
                selectedTasks.push(taskId);
                console.log(selectedTasks);
            } else {
                selectedTasks.splice(index, 1);
            }
        }

        // Function to update the delete button popup
        function updateDeletePopup() {
            if (selectedTasks.length > 0) {
                $("#deletePopup").show();
            } else {
                $("#deletePopup").hide();
            }
        }

        // Handle task checkbox click event
        $(document).on("change", ".task-checkbox", function () {
            const taskId = $(this).data("task-id");
            toggleTaskSelection(taskId);
            updateDeletePopup();
        });

        // Confirm delete and send an AJAX request
        $("#confirmDelete").click(function () {
            // Add your AJAX request to delete the selected tasks here
            console.log("Deleting tasks: " + selectedTasks.join(", "));
            // Clear the selected tasks array
            selectedTasks.length = 0;
            updateDeletePopup();
            $(".task-checkbox").prop("checked", false); // Uncheck all checkboxes
            // Hide the delete button popup
            $("#deletePopup").hide();
        });

        // Cancel delete and hide the popup
        $("#cancelDelete").click(function () {
            // Clear the selected tasks array
            selectedTasks.length = 0;
            updateDeletePopup();
            $(".task-checkbox").prop("checked", false); // Uncheck all checkboxes
            // Hide the delete button popup
            $("#deletePopup").hide();
        });

        // Function to fetch task details using AJAX
        function fetchTaskDetails(taskId) {
            // Clear previous error messages
            $('.error-message').text('');

            $.ajax({
                url: 'partial/task_partial/fetch_task_details.php', // Replace with the URL to your fetch task details PHP file
                method: 'GET',
                data: { task_id: taskId },
                success: function (response) {
                    console.log("--")
                    console.log(response);
                    console.log("--")

                    // Handle the response and populate the edit form here
                    const taskDetails = JSON.parse(response);
                    populateEditForm(taskDetails);
                },
                error: function (xhr, status, error) {
                    // Handle the error if needed
                    console.error('Error fetching task details:', error);
                }
            });
        }

        // Function to populate the edit form with task details
        function populateEditForm(taskDetails) {
            console.log("pemba");
            console.log(taskDetails);
            console.log(taskDetails['task_id']);
            console.log("pemba");
            $('#editTaskName').val(taskDetails.task_name);
            $('#editTaskDescription').val(taskDetails.task_description);

            // Set the assignee select option
            var assigneeSelect = $('#editAssignee');

            // Check if the "Select Assignee" option already exists
            if (assigneeSelect.find('option[value="0"]').length === 0) {
                // If it doesn't exist, add "Select Assignee" option
                assigneeSelect.append($('<option>', {
                    value: '0',
                    text: 'Select Assignee',
                    hidden: "hidden"
                }));
            }

            // Check if the "User not found" option already exists
            if (assigneeSelect.find('option[value="-1"]').length === 0) {
                // If it doesn't exist, add "Select Assignee" option
                assigneeSelect.append($('<option>', {
                    value: '-1',
                    text: 'Assignee not found',
                    hidden: "hidden"
                }));
            }

            // Set the selected option based on response
            if (taskDetails['assignee'] == null) {
                // If assignee is null, select "Select Assignee"
                $('#editAssignee').val('0');
            } else {
                check_assignee_exists(taskDetails['task_id'], taskDetails['assignee']);
            }

            // $('#editAssignee').val(taskDetails.assignee);
            $('#editStartDate').val(taskDetails.start_date);
            $('#editEndDate').val(taskDetails.end_date);

            // Set the status select option
            var statusSelect = $('#editStatus');

            // Check if the "Select Status" option already exists
            if (statusSelect.find('option[value="0"]').length === 0) {
                // If it doesn't exist, add "Select Status" option
                statusSelect.append($('<option>', {
                    value: '0',
                    text: 'Select Status',
                    hidden: "hidden"
                }));
            }

            // Set the selected option based on response
            if (taskDetails['status'] == null || taskDetails['status'] == 'New') {
                // If status is null, select "Select Status"
                $('#editStatus').val('0');
            } else {
                $('#editStatus').val(taskDetails.status);
            }

            // Set the priority select option
            var prioritySelect = $('#editPriority');

            // Check if the "Select priority" option already exists
            if (prioritySelect.find('option[value="0"]').length === 0) {
                // If it doesn't exist, add "Select priority" option
                prioritySelect.append($('<option>', {
                    value: '0',
                    text: 'Select Priority',
                    hidden: "hidden"
                }));
            }

            // Set the selected option based on response
            if (taskDetails['priority'] == null) {
                // If priority is null, select "Select priority"
                $('#editPriority').val('0');
            } else {
                $('#editPriority').val(taskDetails.priority);
            }

        }

        // Function to format a date range
        function formatDateRange(startDate, endDate) {
            if (!startDate || !endDate) {
                return "n/a";
            }

            const start = new Date(startDate);
            const end = new Date(endDate);

            const startYear = start.getFullYear();
            const endYear = end.getFullYear();

            const formattedStart = start.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric'
            });

            const formattedEnd = end.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric'
            });

            if (startYear === endYear) {
                return `${formattedStart} - ${formattedEnd}`;
            } else {
                return `${formattedStart}, ${startYear} - ${formattedEnd}, ${endYear}`;
            }
        }

        function check_assignee_exists(taskId, assignee) {
            $.ajax({
                url: 'partial/project_partial/check_assignee_exists_inproject.php', // Replace with the URL to your fetch task details PHP file
                method: 'GET',
                data: { project_id: <?php echo $project_id; ?>, task_id: taskId
            },
                dataType: 'json',
                success: function (exists_assignee_response) {
                    console.log(exists_assignee_response.result);
                    if (exists_assignee_response.result == true) {
                        // Assign the assignee value
                        $('#editAssignee').val(assignee);
                    } else {
                        // If assignee is not found, select "Assignee not found"
                        $('#editAssignee').val('-1');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle the error if needed
                    console.error('Error fetching task details:', xhr.responseText);
                    console.error('Status:', status);
                    console.error('Error:', error);
                }
        });
    }
    </script>