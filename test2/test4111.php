<!DOCTYPE html>
<html>

<head>
    <title>Sortable Tasks Table with Collapsible Sections</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add jQuery UI library -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        /* Styles for the slide-in popup */
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
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the 'project_id' parameter is present in the URL
    if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
        $project_id = $_GET['project_id'];

        // Prepare and execute the SQL query using a prepared statement
        $stmt = $conn->prepare("SELECT * FROM Tasks WHERE project_id = ?");
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
        <h3 id="taskNamePopup"></h3>
        <p id="taskDescriptionPopup"></p>
        <button id="closeButton">Close</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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

        // JavaScript for handling task click and displaying popup
        $(document).ready(function () {
            $('.sortable tr').click(function () {
                // Get the task details from the clicked row
                const taskId = $(this).attr('data-task-id');
                const taskName = $(this).find('td:nth-child(1)').text(); // Use 1 instead of 4 for task name
                const taskDescription = $(this).find('td:nth-child(2)').text(); // Use 2 instead of 5 for task description

                // Set the task details in the popup
                $('#taskNamePopup').text(taskName); // Change ID to 'taskNamePopup'
                $('#taskDescriptionPopup').text(taskDescription); // Change ID to 'taskDescriptionPopup'

                // Show the popup with animation
                $('#taskPopup').addClass('active');
            });

            // Close the popup when the close button is clicked
            $('#closeButton').click(function () {
                // Hide the popup with animation
                $('#taskPopup').removeClass('active');
            });
        });
    </script>
</body>

</html>