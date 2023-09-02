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

<div class="loginregister-form-container">
    <div class="heading-content">
        <div class="heading-style">
            <p>Login</p>
        </div>
    </div>
    <div class="LR-form">
        <form id="loginForm" action="partial/login.php" method="post">
            <?php if (isset($_GET['project_id'])) { ?>
                <input type="hidden" name="project_id" value="<?php echo $_GET['project_id']; ?>">
            <?php } ?>

            <div class="form-group">
                <input type="text" placeholder="Username" id="username" name="username">
            </div>

            <div class="form-group">
                <input type="password" placeholder="Password" id="password" name="password" required>
            </div>

            <input type="submit" placeholder="" id="submitButton" value="Login">
        </form>
        <div class="div-space-top"></div>
        <div class="div-space-top"></div>
        <div class="div-space-top"></div>
        <div class="div-space-top"></div>
        <div class="donthave_acc">
            <p>Don't have an account? <a href="<?php
            if (isset($_GET['project_id']) && isset($_GET['invite'])) {
                echo 'register_form.php?project_id=' . $_GET['project_id'] . '&invite=true';
            } else {
                echo 'register_form.php';
            }
            ?>">Sign Up</a></p>

        </div>

    </div>
</div>