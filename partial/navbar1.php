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

        <div class="profile-pic">
            <button>PT</button>
        </div>
    </div>


    <script src="static/js/main.js"></script>