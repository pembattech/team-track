<!DOCTYPE html>
<html>

<head>
    <title>Sortable Tasks Table with Collapsible Sections</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add jQuery UI library -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        /* Styles for the slide-in popup */
        .task-popup {
            position: fixed;
            top: 10%;
            right: -440px;
            width: 400px;
            height: 100%;
            background-color: brown;
            color: black;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: right 0.3s ease-in-out;
        }

        /* Show the slide-in popup */
        .task-popup.active {
            right: 0;
        }
    </style>
</head>

<body>
    <?php
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "test";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the 'project_id' parameter is present in the URL
    if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
        $project_id = $_GET['project_id'];

        // Prepare and execute the SQL query using a prepared statement
        $stmt = $conn->prepare("SELECT t.*, u.username AS assignee_name FROM Tasks t
                           LEFT JOIN Users u ON t.assignee = u.user_id
                           WHERE t.project_id = ?");
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

    $conn->close();

    // Function to update the task's status based on the completion status
    function getTaskStatusClass($status)
    {
        if ($status === 'Done') {
            return 'completed';
        } else {
            return 'incomplete';
        }
    }
    ?>
    <?php if (isset($tasksBySection)): ?>
        <?php if (!empty($tasksBySection)): ?>
            <?php foreach ($tasksBySection as $section => $tasks): ?>
                <div class="collapsible">
                    <h2>
                        <?php echo $section; ?>
                    </h2>
                    <table class="sortable" data-section="<?php echo $section; ?>">
                        <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Task Description</th>
                                <th>Assignee</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr data-task-id="<?php echo $task['task_id']; ?>"
                                    class="<?php echo getTaskStatusClass($task['status']); ?>">
                                    <td>
                                        <?php echo $task['task_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $task['task_description']; ?>
                                    </td>
                                    <td>
                                        <?php echo isset($task['assignee_name']) ? $task['assignee_name'] : 'Not Assigned'; ?>
                                    </td>
                                    <td>
                                        <?php echo $task['start_date']; ?>
                                    </td>
                                    <td>
                                        <?php echo $task['end_date']; ?>
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
        <!-- <div class="heading-content">
            <div class="heading-style">
                <p>Edit Task</p>
            </div>
            <div class="bottom-line"></div>
            <div class="div-space-top"></div> -->
            <button type="button" id="closeButton">Close</button>
            <button type="button" id="deleteButton">Delete Task</button>
            <!-- <div class="div-space-top"></div>
        </div>
        <div class="bottom-line"></div>
        <div class="div-space-top"></div>
        <form id="editTaskForm">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <input type="hidden" id="editTaskId" name="task_id">
            <label for="editTaskName">Task Name:</label>
            <input type="text" id="editTaskName" name="task_name" required>
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
        </form> -->
    </div>

    <script>
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
                update: function (event, ui) {
                    // Get the dragged task's ID
                    const taskId = ui.item.attr('data-task-id');
                    // Get the destination section's ID
                    const sectionId = ui.item.closest('.collapsible').find('h2').text().trim();
                    // Update the task's section in the database using an AJAX request
                    $.ajax({
                        url: 'update_task_section.php', // Replace with the URL to your update task section PHP file
                        method: 'POST',
                        data: {
                            task_id: taskId,
                            section: sectionId
                        },
                        success: function (response) {
                            // Handle the response if needed
                            console.log('Task section updated successfully.');
                        },
                        error: function (xhr, status, error) {
                            // Handle the error if needed
                            console.error('Error updating task section:', error);
                        }
                    });
                }
            });
        });

        // Call the fetchTasks function on page load
        $(document).ready(function () {
            fetchTasks();
        });

        // Variable to store the current task ID
        let currentTaskId;

        // JavaScript for handling task click and displaying popup
        $(document).ready(function () {
            $('.sortable tr').click(function () {
                // Get the task details from the clicked row
                const taskId = $(this).attr('data-task-id');
                const taskName = $(this).find('td:nth-child(1)').text();
                const taskDescription = $(this).find('td:nth-child(2)').text();
                // const startDate = $(this).find('td:nth-child(3)').text();
                // const endDate = $(this).find('td:nth-child(4)').text();
                // const status = $(this).find('td:nth-child(5)').text();
                // const priority = $(this).find('td:nth-child(6)').text();

                // Set the task details in the edit popup form
                // $('#editTaskId').val(taskId);
                $('#editTaskName').val(taskName);
                $('#editTaskDescription').val(taskDescription);
                // $('#editStartDate').val(startDate);
                // $('#editEndDate').val(endDate);
                // $('#editStatus').val(status);
                // $('#editPriority').val(priority);


                // Store the task ID in the variable
                currentTaskId = taskId;

                // Show the popup with animation
                $('#taskPopup').addClass('active');

                // // Fetch task details and populate the edit form
                // fetchTaskDetails(taskId);
            });

            // // Submit the edited task details when the form is submitted
            // $('#editTaskForm').submit(function (event) {
            //     event.preventDefault();

            //     // Get the form data
            //     const formData = $(this).serialize();

            //     // Send an AJAX request to update the task details
            //     $.ajax({
            //         url: 'update_task.php', // Replace with the URL to your update task PHP file
            //         method: 'POST',
            //         data: formData,
            //         success: function (response) {
            //             // Handle the response if needed
            //             console.log('Task updated successfully.');
            //             // Hide the edit popup with animation
            //             $('#taskPopup').removeClass('active');
            //             // Fetch tasks again to update the list
            //             fetchTasks();
            //         },
            //         error: function (xhr, status, error) {
            //             // Handle the error if needed
            //             console.error('Error updating task:', error);
            //         }
            //     });
            // });

            // Close the popup when the close button is clicked
            $('#closeButton').click(function () {
                // Hide the popup with animation
                $('#taskPopup').removeClass('active');
            });

            // Delete the task when the delete button is clicked
            $('#deleteButton').click(function () {
                // Get the task ID from the variable
                const taskId = currentTaskId;

                // Send an AJAX request to delete the task
                $.ajax({
                    url: 'delete_task.php', // Replace with the URL to your delete task PHP file
                    method: 'POST',
                    data: {
                        task_id: taskId
                    },
                    success: function (response) {
                        // Handle the response if needed
                        console.log('Task deleted successfully.');
                        // Hide the popup with animation
                        $('#taskPopup').removeClass('active');
                        // Fetch tasks again to update the list
                        // fetchTasks();
                    },
                    error: function (xhr, status, error) {
                        // Handle the error if needed
                        console.error('Error deleting task:', error);
                    }
                });
            });

        });

        // Function to fetch task details using AJAX
        function fetchTaskDetails(taskId) {
            $.ajax({
                url: 'fetch_task_details.php', // Replace with the URL to your fetch task details PHP file
                method: 'GET',
                data: { task_id: taskId },
                dataType: 'json',
                success: function (response) {
                    // Populate the form fields with the fetched task details
                    $('#editTaskId').val(response.task_id);
                    $('#editTaskName').val(response.task_name);
                    $('#editTaskDescription').val(response.task_description);
                    $('#editStartDate').val(response.start_date);
                    $('#editEndDate').val(response.end_date);
                    $('#editStatus').val(response.status);
                    $('#editPriority').val(response.priority);
                },
                error: function (xhr, status, error) {
                    // Handle the error if needed
                    console.error('Error fetching task details:', error);
                }
            });
        }

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
                url: 'fetch_tasks.php',
                method: 'GET',
                data: { project_id: project_id },
                success: function (response) {
                    // Clear the existing tasks
                    $('.collapsible table tbody').empty();

                    $.each(response, function (section, tasks) {
                        const tableBody = $('.collapsible table[data-section="' + section + '"] tbody');
                        $.each(tasks, function (index, task) {
                            const assigneeName = task.assignee_name || 'Not Assigned';
                            const statusClass = task.status === 'Done' ? 'completed' : 'incomplete';
                            const row = `
                                <tr data-task-id="${task.task_id}" class="${statusClass}">
                                    <td>${task.task_name}</td>
                                    <td>${task.task_description}</td>
                                    <td>${assigneeName}</td>
                                    <td>${task.start_date}</td>
                                    <td>${task.end_date}</td>
                                    <td>${task.status}</td>
                                    <td>${task.priority}</td>
                                </tr>`;
                            tableBody.append(row);
                        });
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching tasks:', error);
                }
            });
        }
    </script>
</body>

</html>