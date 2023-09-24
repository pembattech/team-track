<?php
require_once '../config/connect.php';
session_start();

// Function to sanitize user inputs
function sanitize_input($input)
{
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

// Function to handle user login
function login_user($username, $password, $project_id)
{
    global $connection;

    // Initialize the response array
    $response = array();

    // Sanitize user inputs to prevent SQL injection
    $username = sanitize_input($username);

    // Check if the username exists in the 'Users' table
    $sql_check_username = "SELECT * FROM Users WHERE username = '$username'";
    $result_check_username = mysqli_query($connection, $sql_check_username);

    if (mysqli_num_rows($result_check_username) > 0) {
        $user = mysqli_fetch_assoc($result_check_username);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, store user details in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            if ($project_id != null) {
                // Set success message in response
                $response['status'] = 'success';
                $response['message'] = 'Login successful';
                $response['redirect'] = "project.php?project_id=$project_id&invite=true&verify=false";
            } else {
                // Set success message in response
                $response['status'] = 'success';
                $response['message'] = 'Login successful';
                $response['redirect'] = 'home.php';
            }
        } else {
            // Set error message in response
            $response['status'] = 'error';
            $response['message'] = 'Invalid password';
        }
    } else {
        // Set error message in response
        $response['status'] = 'error';
        $response['message'] = 'Username not found';
    }

    return $response;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $project_id = $_POST['project_id'];

    // Call the function to login the user
    $login_result = login_user($username, $password, $project_id);

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($login_result);
}
?>
