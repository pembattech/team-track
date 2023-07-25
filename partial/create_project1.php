<?php
echo "hello";
?>

<?php
session_start();
$user_id = $_SESSION['user_id'];
echo $user_id;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = $_POST['project_name'];
    $user_id = $_SESSION['user_id'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = 'Planning'; // Default status set to 'Planning'
    echo $user_id, $project_name;
    echo $project_name, $description, $start_date, $end_date, $status, $user_id;
    $insert_project_query = "INSERT INTO Projects (project_name, description, start_date, end_date, status)
                            VALUES ('$project_name', '$description', '$start_date', '$end_date', '$status')";

    echo $insert_project_query;
    if ($connection->query($insert_project_query) === TRUE) {
        $project_id = $connection->insert_id;
        echo $project_id;
    } else {
        echo "error";
    }

}
?>