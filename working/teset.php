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
            /* ... Existing styles ... */
        }

        /* Show the slide-in popup */
        .task-popup.active {
            /* ... Existing styles ... */
        }

        /* Make the table header sticky */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        /* ... Existing styles ... */
    </style>
</head>

<body>
    <!-- ... Existing HTML code ... -->

    <?php if (isset($tasksBySection)): ?>
        <?php if (!empty($tasksBySection)): ?>
            <table class="sortable">
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
                <?php foreach ($tasksBySection as $section => $tasks): ?>
                    <tbody data-section="<?php echo $section; ?>">
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
                                    <?php echo $task['assignee']; ?>
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
                                <!-- <td>
                                    <button class="delete-btn" data-task-id="<?php echo $task['task_id']; ?>">Delete</button>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No tasks assigned to this project.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Slide-in popup to display task description -->
    <!-- ... Existing popup code ... -->

</body>

</html>
