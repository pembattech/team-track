<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/connect.php';

// Start the session to access session data
session_start();

function getUserName($user_id)
{
    global $connection;

    $sql = "SELECT name FROM Users WHERE user_id = $user_id";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Team member"; // Default if user not found
    }
}

// Check if the form data has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the task ID and project ID from the form data
    $task_id = $_POST["task_id"];
    $project_id = $_POST["project_id"];
    $projectowner_id = $_POST['projectowner_id'];
    $task_name = $_POST["task_name"];
    $task_description = $_POST["task_description"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $status = $_POST["status"];
    $priority = $_POST["priority"];
    $assignee = $_POST["assignee"];

    $loggedInUserId = $_SESSION['user_id'];

    if ($projectowner_id != $loggedInUserId) {
        // Get the existing task data from the database
        $fetch_task_query = "SELECT * FROM Tasks WHERE task_id = ?";
        $stmt = $connection->prepare($fetch_task_query);

        if ($stmt) {
            $stmt->bind_param("i", $task_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $old_task_data = $result->fetch_assoc();
                $old_task_name = $old_task_data['task_name'];
                $old_task_description = $old_task_data['task_description'];
                $old_assignee = $old_task_data['assignee'];
                $old_start_date = $old_task_data['start_date'];
                $old_end_date = $old_task_data['end_date'];
                $old_status = $old_task_data['status'];
                $old_priority = $old_task_data['priority'];

                // Compare old and new values for each field
                $changes = array();

                if ($old_task_name != $task_name) {
                    $changes[] = "Task Name changed from '$old_task_name' to '$task_name'";
                }

                if ($old_task_description != $task_description) {
                    $changes[] = "Task Description changed from '$old_task_description' to '$task_description'";
                }

                if ($old_assignee != $assignee) {
                    $changes[] = "Assignee changed from '{$old_assignee}' to '{$assignee}'";
                }

                if ($old_start_date != $start_date) {
                    $changes[] = "Start Date changed from '{$old_start_date}' to '{$start_date}'";
                }

                if ($old_end_date != $end_date) {
                    $changes[] = "End Date changed from '{$old_end_date}' to '{$end_date}'";
                }

                if ($old_status != $status) {
                    $changes[] = "Status changed from '{$old_status}' to '{$status}'";
                }

                if ($old_priority != $priority) {
                    $changes[] = "Priority changed from '{$old_priority}' to '{$priority}'";
                }

                // Construct a message summarizing the changes including task name and updater
                $change_message = "Task '$task_name' updated by " . getUserName($loggedInUserId) . ": " . implode(', ', $changes);

                // Insert the change message into the Messages table using a prepared statement
                $insert_message_query = "INSERT INTO Messages (task_id, recipient_id, text, is_task_msg) VALUES (?, ?, ?, 1)";
                $stmt = $connection->prepare($insert_message_query);

                if ($stmt) {
                    $stmt->bind_param("iis", $task_id, $projectowner_id, $change_message);
                    if ($stmt->execute()) {
                        // Message inserted successfully, proceed to update the task
                        // Construct the SQL query
                        $update_task_query = "UPDATE Tasks SET task_name = ?, task_description = ?, assignee = ?, start_date = ?, end_date = ?, status = ?, priority = ? WHERE task_id = ?";
                        $stmt = $connection->prepare($update_task_query);

                        if ($stmt) {
                            $stmt->bind_param("sssssssi", $task_name, $task_description, $assignee, $start_date, $end_date, $status, $priority, $task_id);
                            if ($stmt->execute()) {
                                // Check if any rows were affected
                                if ($stmt->affected_rows > 0) {
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'success', 'message' => "Task '$task_name' updated successfully."));
                                } else {
                                    // Task update failed
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'error', 'message' => 'An error occurred while updating the task.'));
                                }
                            } else {
                                // Error executing the task update query
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'error', 'message' => 'An error occurred while updating the task.'));
                            }
                        } else {
                            // Error preparing the task update query
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'error', 'message' => 'An error occurred while preparing the task update.'));
                        }
                    } else {
                        // Error executing the message insertion query
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => 'An error occurred while inserting the message.'));
                    }
                } else {
                    // Error preparing the message insertion query
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'An error occurred while preparing the message insertion.'));
                }
            }
        }
    }
}

$connection->close();
?>