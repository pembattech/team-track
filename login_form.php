<?php
// Start the session to access session data
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in, redirect to home.php
    header("Location: home.php");
    exit();
}

include 'base.php';
?>

<title>TeamTrack</title>

<style>
    .LR-form #loginForm .error-message {
        font-size: 14px;
    }
</style>
<div class="loginregister-form-container">
    <div class="heading-content">
        <div class="heading-style">
            <p>Login</p>
        </div>
    </div>
    <div class="LR-form">
        <form id="loginForm" novalidate>
            <div class="form-group">
                <input type="text" placeholder="Username" id="username" name="username">
                <div id="usernameError" class="error-message"></div> <!-- Error message for username -->
            </div>

            <div class="div-space-top"></div>
            <div class="form-group">
                <input type="password" placeholder="Password" id="password" name="password" required>
                <div id="passwordError" class="error-message"></div> <!-- Error message for password -->
            </div>

            <div class="div-space-top"></div>
            <input type="submit" placeholder="" id="submitButton" value="Login">
            <p class="error-message error-report"></p>
        </form>

        <div class="div-space-top"></div>
        <div class="div-space-top"></div>
        <div class="div-space-top"></div>
        <div class="div-space-top"></div>
        <div class="donthave_acc">
            <p>Don't have an account? <a href="<?php
            if (isset($_GET['project_id']) && isset($_GET['invite'])) {
                echo 'register_form.php?project_id=' . $_GET['project_id'] . '&invite=true&verify=false';
            } else {
                echo 'register_form.php';
            }
            ?>">Sign Up</a></p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Function to validate username
        function validateUsername() {
            const username = $('#username').val();
            if (username.length === 0) {
                $('#usernameError').text('Please enter a username');
                return false;
            } else {
                $('#usernameError').text('');
                return true;
            }
        }

        // Function to validate password
        function validatePassword() {
            const password = $('#password').val();
            if (password.length === 0) {
                $('#passwordError').text('Please enter a password');
                return false;
            } else {
                $('#passwordError').text('');
                return true;
            }
        }

        // Clear error messages when the user starts typing
        $('#username').on('input', function () {
            $('#usernameError').text('');
            $('.error-report').text('');
        });

        $('#password').on('input', function () {
            $('#passwordError').text('');
            $('.error-report').text('');
        });

        // Attach a submit event listener to the form
        $('#loginForm').submit(function (event) {
            // Prevent the default form submission
            event.preventDefault();

            const project_id = <?php echo isset($_GET['project_id']) ? $_GET['project_id'] : 'null'; ?>;

            // Validate the form fields
            const isUsernameValid = validateUsername();
            const isPasswordValid = validatePassword();

            if (!isUsernameValid && !isPasswordValid) {
                // Display error messages for both fields
                $('#usernameError').text('Please enter a username');
                $('#passwordError').text('Please enter a password');
            } else if (!isUsernameValid & isPasswordValid) {
                // Display an error message for the username field
                $('#usernameError').text('Please enter a username');
            } else if (!isPasswordValid & isUsernameValid) {
                // Display an error message for the password field
                $('#passwordError').text('Please enter a password');
            } else {
                if (isUsernameValid && isPasswordValid) {

                    // If both fields are valid, proceed with the login using AJAX
                    $.ajax({
                        type: 'POST',
                        url: 'partial/login.php',
                        data: {
                            username: $('#username').val(),
                            password: $('#password').val(),
                            project_id: project_id
                        },
                        dataType: 'json',
                        success: function (response) {
                            console.log(response);
                            if (response.status === 'success') {
                                window.location.href = response.redirect;
                                
                            } else if (response.status === 'error') {
                                // Display an error message for login failure
                                $('.error-report').text('Login failed. Please check your credentials.');
                            }

                        },
                        error: function () {
                            // Display an error message for any AJAX errors
                            $('.error-report').text('An error occurred during login.');
                        }
                    });
                }
            }
        });
    });

</script>