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
function login_user($username, $password)
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

            // Redirect to the user's home or desired page after login
            header("Location: ../home.php");
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
    
    // Call the function to login the user
    $login_result = login_user($username, $password);

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

