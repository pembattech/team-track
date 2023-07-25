<link rel="stylesheet" href="static/css/styles.css">
<link rel="stylesheet" href="static/css/tab-style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

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
        background-color: #f9f9f9;
        min-width: 120px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        top: 88px;
        border-radius: 5px;
        right: 40px;
    }

    .popup-menu a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #333;
    }

    .popup-menu a:hover {
        background-color: #ddd;
    }
</style>

<?php include 'config/connect.php'; ?>
<?php include 'partial/utils.php'; ?>


<script src="static/js/main.js"></script>