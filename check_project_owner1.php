<?php
session_start();

// Assuming you have a database connection established
$connection = mysqli_connect("localhost", "root", "", "teamtrack");

$loggedInUserId = $_SESSION['user_id'];

// User is logged in and their ID matches the requested user's ID
$query = "SELECT is_projectowner FROM ProjectUsers WHERE user_id = $loggedInUserId";

$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $isOwner = $row['is_projectowner'] == 1 ? true : false;
    echo json_encode(['is_owner' => $isOwner]);
} else {
    echo json_encode(['is_owner' => false]);
}

mysqli_close($connection);
?>