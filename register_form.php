<?php

// Start the session to access session data
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in, redirect to home.php
    header("Location: home.php");
    exit();
}

include 'base.php'; ?>

<title>User Registration Form</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<div class="loginregister-form-container">
    <div class="heading-content">
        <div class="heading-style">
            <p>User Registration</p>
        </div>
    </div>
    <div class="LR-form">
        <form id="registrationForm" action="partial/register.php" method="post">
            <?php if (isset($_GET['project_id']) && isset($_GET['invite'])) { ?>
                <input type="hidden" name="project_id" value="<?php echo $_GET['project_id']; ?>">
                <input type="hidden" name="invite" value="<?php echo $_GET['invite']; ?>">
            <?php } ?>
            <div class="form-group">
                <input type="text" placeholder="Name" id="name" name="name" required>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Username" id="username" name="username" required>
            </div>

            <div class="form-group">
                <!-- <p class="status" id="emailStatus"></p> -->
                <input type="email" placeholder="Email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <input type="password" placeholder="Password" id="password" name="password" required>
            </div>

            <input type="submit" placeholder="" id="submitButton" value="Register" disabled>
            <div class="div-space-top"></div>
            <div class="div-space-top"></div>
            <div class="div-space-top"></div>
            <div class="div-space-top"></div>
            <div class="already">
                <p>Already have an account? <a href="login_form.php">Login</a></p>
            </div>
        </form>
    </div>
</div>


<script>
    // Function to update input field classes based on availability status
    function updateInputStatus(inputElement, status) {
        if (status === 'available') {
            inputElement.addClass('available');
            inputElement.removeClass('taken');
        } else {
            inputElement.removeClass('available');
            inputElement.addClass('taken');
        }
    }

    // Front-end validation using JavaScript (AJAX)
    $(document).ready(function () {
        // Function to validate username
        function validateUsername() {
            const username = $('#username').val();
            if (username.length < 4) {
                $('#usernameError').text('Username must be at least 4 characters long');
                return false;
            } else {
                $('#usernameError').text('');
                return true;
            }
        }

        // Function to validate email
        function validateEmail() {
            const email = $('#email').val();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                $('#emailError').text('Invalid email address');
                return false;
            } else {
                $('#emailError').text('');
                return true;
            }
        }

        // Function to check if the username is available
        function checkUsernameAvailability() {
            const username = $('#username').val();
            return $.ajax({
                type: 'POST',
                url: 'partial/validation_check/check_username.php',
                data: { username: username },
            });
        }

        // Function to check if the email is available
        function checkEmailAvailability() {
            const email = $('#email').val();
            return $.ajax({
                type: 'POST',
                url: 'partial/validation_check/check_email.php',
                data: { email: email },
            });
        }

        // Attach event listeners to input fields
        $('#username').on('input', function () {
            validateUsername();
            checkUsernameAvailability().done(function (response) {
                if (response === 'taken') {
                    $('#usernameStatus').text('Username is already taken');
                    updateInputStatus($('#username'), 'taken');
                } else {
                    $('#usernameStatus').text('Username is available');
                    updateInputStatus($('#username'), 'available');
                }
                enableDisableSubmitButton(); // Call the function to enable/disable the submit button
            });
        });

        $('#email').on('input', function () {
            validateEmail();
            checkEmailAvailability().done(function (response) {
                if (response === 'taken') {
                    $('#emailStatus').text('Email is already registered');
                    updateInputStatus($('#email'), 'taken');
                } else {
                    $('#emailStatus').text('Email is available');
                    updateInputStatus($('#email'), 'available');

                }
                enableDisableSubmitButton(); // Call the function to enable/disable the submit button
            });
        });

        function enableDisableSubmitButton() {
            const username = $('#username').val();
            const email = $('#email').val();

            // Enable the submit button only if both username and email are available and valid
            if (username.length >= 4 && validateEmail()) {
                // Check the availability of username and email
                $.when(checkUsernameAvailability(), checkEmailAvailability()).done(function (usernameResponse, emailResponse) {
                    if (usernameResponse[0] === 'available' && emailResponse[0] === 'available') {
                        $('#submitButton').prop('disabled', false);
                    } else {
                        $('#submitButton').prop('disabled', true);
                    }
                });
            } else {
                // Otherwise, disable the submit button
                $('#submitButton').prop('disabled', true);
            }
        }

        // $('#registrationForm').submit(function (event) {
        //     event.preventDefault();
        //     // Validate both username and email before submitting the form
        //     if (validateUsername() && validateEmail()) {
        //         console.log("submit button clicked")
        //     } else {
        //         // If either username or email validation fails, prevent form submission
        //         // You can display an error message or take appropriate action here
        //         alert('Please fill in the required fields correctly.');
        //     }
        // });

    });
</script>