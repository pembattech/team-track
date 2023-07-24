<?php
// Connect to the database
require_once 'config/connect.php';

// Function to sanitize user inputs
function sanitize_input($input)
{
    global $connection;
    return mysqli_real_escape_string($connection, $input);
}

// Function to handle user registration
function register_user($username, $email, $password)
{
    global $connection;

    // Sanitize user inputs to prevent SQL injection
    $username = sanitize_input($username);
    $email = sanitize_input($email);

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email is already registered
    $sql_check_email = "SELECT * FROM Users WHERE email = '$email'";
    $result_check_email = mysqli_query($connection, $sql_check_email);
    if (mysqli_num_rows($result_check_email) > 0) {
        return "Email already registered";
    }

    // Insert the new user into the 'Users' table
    $sql_register_user = "INSERT INTO Users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    if (mysqli_query($connection, $sql_register_user)) {
        return "Registration successful";
    } else {
        return "Registration failed";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Call the function to register the user
    $registration_result = register_user($username, $email, $password);

    // Redirect the user after registration
    header("Location: login.php?message=" . urlencode($registration_result));
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
</head>

<body>
    <h2>Registration</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>

</html>
