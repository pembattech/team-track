<title>TeamTrack</title>
<link rel="icon" type="image/x-icon" href="static/image/teamtrack_logo.png">
<link rel="shortcut icon" href="static/image/teamtrack_logo.png" type="image/x-icon">
<link rel="stylesheet" href="static/css/styles.css">
<link rel="stylesheet" href="static/css/tab-style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<style>
    #popup-btn {
        margin-left: auto;
        /* Move the button to the right */
        margin-right: 10px;
        border: none;
        height: 40px;
        width: 40px;
        border-radius: 50%;
        box-shadow: 0px 1px 4px 1px rgba(0, 0, 0, 0.3);
        cursor: pointer;
    }

    /* Additional CSS for the navbar-right container */
    .navbar-right {
        display: flex;
        align-items: center;
    }

    .popup-menu {
        display: none;
        position: absolute;
        background-color: var(--sidebar-bgcolor);
        min-width: 120px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--color-border);
        z-index: 9;
        top: 60px;
        border-radius: 5px;
        right: 40px;
    }

    .popup-menu a {
        display: block;
        padding: 10px;
        font-size: 16px;
        font-family: inherit;
        text-decoration: none;
        color: var(--color-text);
        text-align: center;
    }

    .popup-menu a p:hover {
        border-radius: 5px;
        background-color: var(--color-background-weak);
    }

    .popup_notify {
        display: none;
        position: fixed;
        bottom: 5%;
        left: 0;
        padding: 20px;
        background-color: var(--color-background-weak);
        color: var(--color-text);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        border-top: 5px solid #0063A7;
        border-radius: 5px;
        z-index: 9999;
    }

    .popup_notify.error {
        border-top: 5px solid var(--danger-color);
    }

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

    .LR-form .error-message {
        font-size: 12px;
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
    .LR-form input[type="email"].available,
    .LR-form input[type="password"].available {
        border-color: green;
        /* Change to your preferred green color */
    }

    .LR-form input[type="text"].taken,
    .LR-form input[type="email"].taken,
    .LR-form input[type="password"].taken {
        border-color: red;
        /* Change to your preferred green color */
    }


    .LR-form input[type="submit"]:disabled {
        color: var(--color-text-weak);
        background-color: var(--color-background-weak);
    }

    .LR-form input[type="submit"] {
        width: 100%;
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

<div class="popup_notify" id="popupNotification"></div>


<?php include 'config/connect.php'; ?>
<?php include 'partial/utils.php'; ?>



<script src="static/js/main.js"></script>
<script>
    // Function to display a message in the popup notification
    function displayPopupMessage(message, type) {
        // Display the popup notification with the dynamic message
        var popupNotification = document.getElementById("popupNotification");
        popupNotification.innerText = message;
        console.log(popupNotification);
        popupNotification.style.display = "block";

        const popup = $('#popupNotification');
        popup.html(message);

        if (type === 'success') {
            popup.removeClass('error');
            popup.addClass('success');
        } else if (type === 'error') {
            popup.removeClass('success');
            popup.addClass('error');
        }

        // Show the popup
        popup.slideDown(300);

        setTimeout(function () {
        popup.slideUp(300, function () {
            popupNotification.style.display = "none"; // Hide the popup completely
        });
    }, 5000);
    }


</script>