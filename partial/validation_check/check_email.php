<?php

require_once '../../config/connect.php';
error_reporting(E_ALL);

ini_set('display_errors', 1);


if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'taken'; // Return 'invalid' if the email format is not valid
    } else {
        // Check if the email is already taken
        $check_email_sql = "SELECT * FROM Users WHERE email = '$email'";
        $result = $connection->query($check_email_sql);
        if ($result->num_rows > 0) {
            echo 'taken';
        } else {
            echo 'available';
        }
    }
}
?>