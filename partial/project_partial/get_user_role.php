<?php
// Assuming you have a database connection established
$connection = mysqli_connect("localhost", "root", "", "teamtrack");

$userId = $_POST['user_id']; // Get the user ID from the AJAX request

$query = "SELECT user_role FROM ProjectUsers WHERE user_id = $userId AND is_projectOwner = 0";
$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $userRole = $row['user_role'];
    echo $userRole;
} else {
    echo 'Error';
}

mysqli_close($connection);
?>
