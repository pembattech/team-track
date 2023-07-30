<?php include 'base.php'; ?>
<style>
    .loginregister-form-container .heading-style {
        margin: 0;
        padding: 0 20px;
    }

    .loginregister-form-container {
        position: relative;
        margin: 50px auto;
        padding: 15px;
        background: var(--color-background-weak);
        width: 350px;
        border: 0;
        border-radius: 5px;
        box-shadow: 3px 3px 10px #333;
        color: var(--color-text);
    }

    .LR-form {
        padding: 25px;
    }


    .LR-form input[type="text"],
    .LR-form input[type="text"],
    .LR-form input[type="email"],
    .LR-form input[type="password"] {
        outline: 0;
        color: var(--color-text);
        background-color: var(--sidebar-bgcolor);
        border: 1px solid var(--color-border);
        border-radius: 5px;
        width: 100%;
        padding: 20px;
        margin-bottom: 25px;
        height: 40px;
        -moz-outline-style: none;
    }

    .LR-form input[type="text"]:focus,
    .LR-form input[type="text"]:focus,
    .LR-form input[type="email"]:focus,
    .LR-form input[type="password"]:focus {
        border: 1px solid var(--color-text);
    }

    .LR-form p.status {
        margin: 0;
        padding: 0;
        color: green;
        font-size: 12px;
        /* Change the color to your preferred status color */
    }

    .LR-form input[type="text"].available,
    .LR-form input[type="email"].available {
        border-color: green;
        /* Change to your preferred green color */
    }

    .LR-form input[type="text"].taken,
    .LR-form input[type="email"].taken {
        border-color: red;
        /* Change to your preferred green color */
    }


    .LR-form input[type="submit"]:disabled {
        color: var(--color-text-weak);
        background-color: var(--color-background-weak);
    }

    .LR-form input[type="submit"] {
        outline: 0;
        padding: 3px;
        border: 1px solid var(--color-border);
        border-radius: 5px;
        background-color: var(--color-background-hover);
        color: var(--color-text);
    }

    .LR-form input[type="submit"]:enabled:hover {
        background-color: var(--color-background-weak);

    }
</style>

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