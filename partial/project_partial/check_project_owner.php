<?php
session_start();

// Assuming you have a database connection established
$connection = mysqli_connect("localhost", "root", "", "teamtrack");

$loggedInUserId = $_SESSION['user_id'];

$query = "SELECT is_projectowner, project_id FROM ProjectUsers WHERE user_id = $loggedInUserId";

$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $isOwner = $row['is_projectowner'] == 1 ? true : false;
    $projectOwnerId = $isOwner ? $loggedInUserId : null; // Set project owner ID if user is owner
    $projectId = $row['project_id'];
    echo json_encode(['is_owner' => $isOwner, 'project_owner_id' => $projectOwnerId, 'project_id' => $projectId]);
} else {
    echo json_encode(['is_owner' => false]);
}

mysqli_close($connection);
?>