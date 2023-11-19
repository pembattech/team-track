<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
require_once '../../config/connect.php';
include '../utils.php';

$user_id = $_SESSION['user_id'];

// Fetch project names from the "Projects" table where the user is assigned
$sql = "SELECT P.project_id, P.project_name, P.background_color 
        FROM Projects P
        INNER JOIN ProjectUsers PU ON P.project_id = PU.project_id
        WHERE PU.user_id = $user_id AND P.status = 'active'";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Loop through the results and generate anchor tags for each project
    while ($row = $result->fetch_assoc()) {
        $project_id = $row['project_id'];
        $project_name = $row['project_name'];
        $background_color = $row['background_color'];
        echo '<div class="project-lst" data-project-id=' . $project_id . '>';
        echo '<a href="project.php?project_id=' . $project_id . '" class="project-link" id="link">';
        echo '    <div class="square" style="background-color:' . $background_color . '"></div>';
        echo '    <p class="project-title">' . add_ellipsis($project_name, 20) . '</p>';
        echo '</a>';
        echo '</div>';
    }
} else {
    // If no projects are assigned, display a message or do something else
    echo 'No projects.';
}
?>
