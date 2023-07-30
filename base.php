<title>TeamTrack</title>
<link rel="icon" type="image/x-icon" href="static/image/teamtrack_logo.png">
<link rel="shortcut icon" href="static/image/teamtrack_logo.png" type="image/x-icon">
<link rel="stylesheet" href="static/css/styles.css">
<link rel="stylesheet" href="static/css/tab-style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> -->
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
        z-index: 1;
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
</style>

<div class="popup_notify" id="popupNotification"></div>

<?php include 'config/connect.php'; ?>
<?php include 'partial/utils.php'; ?>


<script src="static/js/main.js"></script>