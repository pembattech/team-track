<?php
// Connect to the database
require_once 'config/connect.php';

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
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the user's home or desired page after login
            header("Location: home.php");
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
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    <?php
    if (isset($_GET['message'])) {
        echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>

</html>
