<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    echo $userId;
    $projectId = $_POST['project_id'];

    $sql_fetch_userrole = "SELECT user_role FROM ProjectUsers WHERE project_id = $projectId AND user_id = $userId";
    $result = mysqli_query($connection, $sql_fetch_userrole);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $userRole = $row['user_role'];

        $response = array(
            "status" => "success",
            "user_role" => $userRole
        );

        // Set the Content-Type header for JSON response
        header('Content-Type: application/json');

        echo json_encode($response);
    } else {
        $response = array(
            "status" => "error"
        );
        // Set the Content-Type header for JSON response
        header('Content-Type: application/json');

        echo json_encode($response);
    }

}
?>