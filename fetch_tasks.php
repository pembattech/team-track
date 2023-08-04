<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
                            <th>Action</th>
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
                                <td>
                                    <button class="delete-btn" data-task-id="<?php echo $task['task_id']; ?>">Delete</button>
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