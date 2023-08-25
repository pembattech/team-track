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


            if ($project_id !== null) {
                header("Location: ../project.php?project_id=" . $project_id . "&verify=false");
            } else {
                header("Location: ../home.php");
            }
            exit();
        } else {
            return "Invalid password";
        }
    } else {
        return "Username not found";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $project_id = isset($_POST["project_id"]) ? $_POST["project_id"] : null;

    // Call the function to login the user
    $login_result = login_user($username, $password, $project_id);

    // // Perform validation
    // $errors = array();

    // if (empty($username)) {
    //     $errors[] = "Username is required.";
    // }

    // if (empty($password)) {
    //     $errors[] = "Password is required.";
    // }

    // // If there are no errors, you can proceed with login logic
    // if (empty($errors)) {
    // }

}
?>




// // login.php
<!-- // 
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $response = array();
// 
//     // Validate username and password (you can add more validation as needed)
//     $username = isset($_POST['username']) ? trim($_POST['username']) : '';
//     $password = isset($_POST['password']) ? $_POST['password'] : '';
// 
//     if (empty($username)) {
//         $response['status'] = 'error';
//         $response['message'] = 'Username is required.';
//     } elseif (empty($password)) {
//         $response['status'] = 'error';
//         $response['message'] = 'Password is required.';
//     } elseif ($username !== 'your_username' || $password !== 'your_password') {
//         // Replace 'your_username' and 'your_password' with your actual valid credentials.
//         $response['status'] = 'error';
//         $response['message'] = 'Invalid username or password.';
//     } else {
//         // Authentication successful
//         $response['status'] = 'success';
//         $response['message'] = 'Login successful.';
//     }
// 
//     // Send the response back to the client as JSON
//     header('Content-Type: application/json');
//     echo json_encode($response);
// } -->