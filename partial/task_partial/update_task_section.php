<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
require_once '../../config/connect.php';
include '../../partial/utils.php';


// Start the session to access session data
session_start();

// Get the task ID and the destination section from the AJAX request
if (isset($_POST['task_id']) && isset($_POST['section']) && isset($_POST['projectowner_id'])) {
    $taskId = $_POST['task_id'];
    $project_id = $_POST['project_id'];
    $section = removeParenthesesWithNumber($_POST['section']);
    $projectowner_id = $_POST['projectowner_id'];

    $loggedInUserId = $_SESSION['user_id'];

    // Get the task's creator ID from the database
    $taskCreatorIdSql = "SELECT task_creator_id FROM Tasks WHERE task_id='$taskId'";
    $taskCreatorResult = $connection->query($taskCreatorIdSql);

    if ($taskCreatorResult->num_rows > 0) {
        $taskCreatorData = $taskCreatorResult->fetch_assoc();
        $taskCreatorId = $taskCreatorData['task_creator_id'];
    }

    // Check if the user is the project owner or the task creator
    if ($projectowner_id == $loggedInUserId || $taskCreatorId == $loggedInUserId) {

        // Check if the section is "Done"
        if ($section == 'Done') {
            // Perform a query to check if all fields are filled for the task
            $checkSql = "SELECT * FROM Tasks WHERE task_id='$taskId' AND (task_name = '' OR assignee IS NULL OR task_description = '' OR start_date IS NULL OR end_date IS NULL)";
            $result = $connection->query($checkSql);


            if ($result->num_rows > 0) {
                // If any required fields are not filled, return an error response
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'All fields must be filled before moving the task to the "Done" section.'));
                exit; // Stop further processing
            }

            // Update the status of the task to "Complete"
            $updateSql = "UPDATE Tasks SET section='$section', status='In Review' WHERE task_id='$taskId'";

            if ($connection->query($updateSql) === TRUE) {
                // Return a success response if the update is successful
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => 'Task section and status updated to complete.'));

                if ($projectowner_id != $loggedInUserId) {

                    $task_name = getTaskInfo($taskId)['task_name'];
                    $messageText = $task_name . " task has been marked as Done.";

                    $insertMessageSql = "INSERT INTO Messages (task_id, text, recipient_id, is_task_msg)
                VALUES ('$taskId', '$messageText', '$projectowner_id', 1)";

                    if ($connection->query($insertMessageSql) === TRUE) {

                        // Add recent activity for moving the task to "Done"
                        $activity_description = "'" . getTaskInfo($taskId)['task_name'] . "' task moved to 'Done' section by user '" . getUserName($loggedInUserId) . "'";
                        addRecentActivity($loggedInUserId, "Task Moved", $activity_description, $project_id, $taskId);

                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'success', 'message' => 'Task marked as Done and message sent to project owner.'));
                    } else {
                        header('Content-Type: application/json');

                        echo json_encode(array('status' => 'error', 'message' => 'Error sending message to project owner: ' . $connection->error));
                    }
                }

            } else {
                // Return an error response if there is an issue with the update
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error updating task: ' . $connection->error));
            }
        } else {
            // Check if the previous section was "Done"
            $previousSectionSql = "SELECT section FROM Tasks WHERE task_id='$taskId'";
            $previousSectionResult = $connection->query($previousSectionSql);

            if ($previousSectionResult->num_rows > 0) {
                $previousSectionData = $previousSectionResult->fetch_assoc();
                $previousSection = $previousSectionData['section'];

                if ($previousSection != $section) {


                    if ($previousSection == 'Done') {
                        // If the previous section was "Done," update the status to "Review"
                        $updateStatusSql = "UPDATE Tasks SET section='$section', status='Pending Approval' WHERE task_id='$taskId'";

                        if ($connection->query($updateStatusSql) === TRUE) {
                            // Add recent activity for moving the task from "Done" to another section
                            $activity_description = "'" . getTaskInfo($taskId)['task_name'] . "' task moved from 'Done' section to " . $section . " by user '" . getUserName($loggedInUserId) . "'";
                            addRecentActivity($loggedInUserId, "Task Moved", $activity_description, $project_id, $taskId);

                            // Return a success response if the update is successful
                            header('Content-Type: application/json');

                            echo json_encode(array('status' => 'success', 'message' => 'Task section updated and status set to review.'));
                        } else {
                            // Return an error response if there is an issue with the update
                            header('Content-Type: application/json');

                            echo json_encode(array('status' => 'error', 'message' => 'Error updating task section and status: ' . $connection->error));
                        }
                    } else {
                        // If the previous section was not "Done," just update the section
                        $updateSql = "UPDATE Tasks SET section='$section' WHERE task_id='$taskId'";

                        if ($connection->query($updateSql) === TRUE) {
                            // Add recent activity for moving the task to a new section
                            $activity_description = "'" . getTaskInfo($taskId)['task_name'] . "' task moved to '" . $section . "' section by user '" . getUserName($loggedInUserId) . "'";
                            addRecentActivity($loggedInUserId, "Task Moved", $activity_description, $project_id, $taskId);

                            // Return a success response if the update is successful
                            header('Content-Type: application/json');

                            echo json_encode(array('status' => 'success', 'message' => 'Task section updated successfully.'));
                        } else {
                            // Return an error response if there is an issue with the update
                            header('Content-Type: application/json');

                            echo json_encode(array('status' => 'error', 'message' => 'Error updating task section: ' . $connection->error));
                        }
                    }
                }

            } else {
                // Return an error response if there is an issue with the update
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error updating task section: ' . $connection->error));
            }
        }
    } else {
        // Return an error response if the required parameters are not provided
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'You are neither the project owner nor the designated assignee for this task.'));
    }

}
$connection->close();
?>