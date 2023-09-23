<?php
require_once '../../config/connect.php';

// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the project has only one member, and that member is the project owner
    $sql_check_project_members = "SELECT COUNT(*) AS member_count
                                   FROM ProjectUsers
                                   WHERE project_id = $project_id AND is_projectowner = 1";

    $result = $connection->query($sql_check_project_members);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $member_count = $row['member_count'];

        if ($member_count == 1) {
            // If there is only one member (the owner), proceed with deletion

            // Delete project users
            $sql_delete_project_users = "DELETE FROM ProjectUsers WHERE project_id = $project_id";
            if ($connection->query($sql_delete_project_users) === FALSE) {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error deleting project users.'));
                exit;
            }

            // Get the projectuser_id(s) associated with the project
            $sql_get_projectuser_ids = "SELECT projectuser_id FROM ProjectUsers WHERE project_id = $project_id";
            $projectuser_ids_result = $connection->query($sql_get_projectuser_ids);

            // Delete all tasks associated with the projectuser_id(s)
            while ($row = $projectuser_ids_result->fetch_assoc()) {
                $projectuser_id = $row['projectuser_id'];
                $sql_delete_tasks = "DELETE FROM Tasks WHERE projectuser_id = $projectuser_id";
                if ($connection->query($sql_delete_tasks) === FALSE) {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'Error deleting project tasks.'));
                    exit;
                }
            }

            // Delete the project
            $sql_delete_project = "DELETE FROM Projects WHERE project_id = $project_id";
            if ($connection->query($sql_delete_project) === TRUE) {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => 'Project and its related tasks deleted successfully.'));
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Error deleting the project.'));
            }
        } else {
            // If there are multiple members, don't allow deletion
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Project can only be deleted if there is only one member (the owner).'));
        }
    } else {
        // Error in counting members
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'Error checking project members.'));
    }
}

?>