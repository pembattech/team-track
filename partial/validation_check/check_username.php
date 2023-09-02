<?php
require_once '../../config/connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    // Check if the username is already taken
    $check_username_sql = "SELECT * FROM Users WHERE username = '$username'";
    $result = $connection->query($check_username_sql);
    if ($result->num_rows > 0) {
        echo 'taken';
    } else {
        echo 'available';
    }
}
?>
