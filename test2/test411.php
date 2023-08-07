<!DOCTYPE html>
<html>

<head>
    <title>Sortable Tasks Table with Collapsible Sections</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add jQuery UI library -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        /* Your CSS styles go here */
        /* ... */
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

    <?php foreach ($tasksBySection as $section => $tasks): ?>
        <div class="collapsible">
            <h2>
                <?php echo $section; ?>
            </h2>
            <table class="sortable" data-section="<?php echo $section; ?>">
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Project ID</th>
                        <th>User ID</th>
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
                                <?php echo $task['task_id']; ?>
                            </td>
                            <td>
                                <?php echo $task['project_id']; ?>
                            </td>
                            <td>
                                <?php echo $task['user_id']; ?>
                            </td>
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
    </script>
</body>

</html>