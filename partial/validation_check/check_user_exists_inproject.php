<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php

function check_user_exists_inproject($project_id)
{
    require_once 'config/connect.php';
    global $connection;

    $user_id = $_SESSION['user_id'];

    $query = "SELECT COUNT(*) FROM ProjectUsers WHERE project_id = $project_id AND user_id = $user_id";

    $result = mysqli_query($connection, $query);

    if ($result) {
        $row = mysqli_fetch_array($result);
        $count = $row[0];
        mysqli_free_result($result);
        echo $count;
        return ($count > 0);
    } else {
        echo "Error in query: " . mysqli_error($connection);
        return false;
    }
}
?>

