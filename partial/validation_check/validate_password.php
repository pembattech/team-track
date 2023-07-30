<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pattern = '/[^a-zA-Z0-9\s]/'; // Matches any character that is not a letter, digit, or space.
    $password = $_POST["password"];
    $response = array(
        "message" => "",
        "length" => false,
        "uppercase" => false,
        "lowercase" => false,
        "digit" => false,
        "specialcharacter" => false
    );

    // Perform your password validation logic here
    if (strlen($password) >= 6) {
        $response["length"] = true;
    }
    if (preg_match("/[A-Z]/", $password)) {
        $response["uppercase"] = true;
    }
    if (preg_match("/[A-Za-z]/", $password)) {
        $response["lowercase"] = true;
    }
    if (preg_match("/\d/", $password)) {
        $response["digit"] = true;
    }
    if (preg_match("/[\W]/", $password)) {
        $response["specialcharacter"] = true;
    }

    $response["message"] = "Password is valid!";
    $_SESSION['valid_password'] = 'truepassword';
    echo json_encode($response);
}
?>