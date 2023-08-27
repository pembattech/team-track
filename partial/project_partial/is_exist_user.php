<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>


<?php
require_once '../../config/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
        $project_id = $_POST["project_id"];

        $query = "SELECT COUNT(*) FROM ProjectUsers pu
                  INNER JOIN Users u ON pu.user_id = u.user_id
                  WHERE pu.project_id = $project_id AND u.email = '$email'";

        $result = mysqli_query($connection, $query);

        if ($result) {
            $row = mysqli_fetch_array($result);
            $count = $row[0];

            $response = array("exists" => ($count > 0));
            echo json_encode($response);
        } else {
            echo "Error in query: " . mysqli_error($connection);
        }

        mysqli_close($connection);
    }
}
?>
