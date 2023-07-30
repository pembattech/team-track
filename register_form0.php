<!DOCTYPE html>
<html>

<head>
    <title>User Registration Form</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>User Registration</h1>
    <form id="registrationForm">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <span class="error" id="usernameError"></span>
        <span class="status" id="usernameStatus"></span>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <span class="error" id="emailError"></span>
        <span class="status" id="emailStatus"></span>
        <!-- Rest of the form fields as before -->

        <input type="submit" id="submitButton" value="Register" disabled> <!-- Add 'id="submitButton"' and 'disabled' attribute -->
    </form>

    <div id="result"></div>

    <script>
        // Front-end validation using JavaScript (AJAX)
        $(document).ready(function () {
            // Function to validate username
            function validateUsername() {
                const username = $('#username').val();
                if (username.length < 4) {
                    $('#usernameError').text('Username must be at least 4 characters long');
                    $('#usernameStatus').text('');
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
                    url: 'partial/validation_check/check_username.php', // Replace with the actual filename of your PHP script
                    data: { username: username },
                });
            }

            // Function to check if the email is available
            function checkEmailAvailability() {
                const email = $('#email').val();
                return $.ajax({
                    type: 'POST',
                    url: 'partial/validation_check/check_email.php', // Replace with the actual filename of your PHP script to check email availability
                    data: { email: email },
                });
            }

            // Attach event listeners to input fields
            $('#username').on('input', function () {
                validateUsername();
                checkUsernameAvailability().done(function (response) {
                    if (response === 'taken') {
                        $('#usernameStatus').text('Username is already taken');
                    } else {
                        $('#usernameStatus').text('Username is available');
                    }
                    enableDisableSubmitButton(); // Call the function to enable/disable the submit button
                });
            });

            $('#email').on('input', function () {
                validateEmail();
                checkEmailAvailability().done(function (response) {
                    if (response === 'taken') {
                        $('#emailStatus').text('Email is already registered');
                    } else {
                        $('#emailStatus').text('Email is available');
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
                    checkUsernameAvailability().done(function (usernameResponse) {
                        checkEmailAvailability().done(function (emailResponse) {
                            if (usernameResponse === 'available' && emailResponse === 'available') {
                                $('#submitButton').prop('disabled', false);
                            } else {
                                $('#submitButton').prop('disabled', true);
                            }
                        });
                    });
                } else {
                    // Otherwise, disable the submit button
                    $('#submitButton').prop('disabled', true);
                }
            }

            $('#registrationForm').submit(function (event) {
                event.preventDefault();
                // Validate both username and email before submitting the form
                if (validateUsername() && validateEmail()) {
                    // Perform form submission logic here (e.g., sending form data to server using AJAX)
                    const formData = {
                        username: $('#username').val(),
                        email: $('#email').val(),
                    };
                    alert(JSON.stringify(formData)); // Example: Displaying the form data as a JSON string
                    // You can add the AJAX code here to submit the data to the server
                } else {
                    // If either username or email validation fails, prevent form submission
                    // You can display an error message or take appropriate action here
                    alert('Please fill in the required fields correctly.');
                }
            });
        });
    </script>
</body>

</html>
