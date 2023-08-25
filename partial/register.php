<?php

require_once '../config/connect.php';

// Start the session
session_start();

// Function to sanitize user inputs
function sanitize_input($input)
{
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}


function generateRandomColor()
{
    $colors = ["#f06a6a", "#ec8d71", "#f1bd6c", "#f8df72", "#aecf55", "#5da283", "#4ecbc4", "#9ee7e3", "#4573d2", "#8d84e8", "#b36bd4", "#f9aaef", "#f26fb2", "#fc979a"];
    $randomIndex = array_rand($colors);
    return $colors[$randomIndex];
}

// Function to handle user registration
function register_user($name, $username, $email, $password)
{
    global $connection;


    // Sanitize user inputs to prevent SQL injection
    $name = sanitize_input(($name));
    $username = sanitize_input($username);
    $email = sanitize_input($email);


    echo $name, $username, $email, $password;


    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    echo $hashed_password;


    $background_color = generateRandomColor();
    echo $background_color;


    // Insert the new user into the 'Users' table
    $sql_register_user = "INSERT INTO Users (name, username, email, password, background_color) VALUES ('$name', '$username', '$email', '$hashed_password', '$background_color')";
    echo $sql_register_user;

    if (mysqli_query($connection, $sql_register_user)) {
        return "Registration successful";
    } else {
        return "Registration failed";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];



    $registration_result = register_user($name, $username, $email, $password);

    // Redirect the user after registration
    header("Location: ../login_form.php?message=" . urlencode($registration_result));
    exit();

}
?>