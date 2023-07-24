<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teamtrack</title>
    <link rel="stylesheet" href="static/css/styles.css">
    <link rel="stylesheet" href="static/css/tab-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <style>
        #popup-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .popup-menu {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            top: 100px;
            right: 45px;
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
</head>

<body>

    <?php include 'config/connect.php'; ?>

    <div class="navbar">
        <button class="collapse-toggle-btn">M</button>

        <div class="create-project-btn overlay-border related-btn-img">
            <img class="svg-img" src="./static/image/add-square.svg" alt="create">
            <p>Create</p>
        </div>

        <div class="search__container">
            <input class="search__input" type="text" placeholder="Search">
        </div>

        <!-- Button to trigger the popup menu -->
        <button id="popup-btn">Menu</button>
        <!-- The popup menu -->
        <div class="popup-menu" id="myPopup">
            <a href="#" onclick="logout()">Logout</a>
            <a href="#" onclick="editProfile()">Profile Edit</a>
        </div>
    </div>


    <script src="static/js/main.js"></script>

