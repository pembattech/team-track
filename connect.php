<?php
// Database credentials
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'teamtrack';

// Create a database connection
$connection = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

else{
    echo "Sucess, $database";
}
?>
