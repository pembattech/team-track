<?php
// Start the session to access session data
session_start();


// Check if the user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in, retrieve session data
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    ?>

    <?php include 'partial/navbar.php'; ?>

    <div class="container">
        <?php include 'partial/sidebar.php'; ?>

        <div class="main-content">
            <div class="heading-content">
                <div class="heading-style">
                    <p>Home</p>
                </div>
            </div>
            <div class="daydate-greet-container">
                <div id="date"></div>
                <div id="greeting"></div>
            </div>
        </div>
    </div>

    <script>
        function getGreeting() {
            const currentDate = new Date();
            const currentHour = currentDate.getHours();

            let greeting;
            if (currentHour >= 5 && currentHour < 12) {
                greeting = "Good morning";
            } else if (currentHour >= 12 && currentHour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }

            return greeting;
        }

        function formatDate() {
            const currentDate = new Date();
            const options = { weekday: 'long', month: 'long', day: 'numeric' };
            return currentDate.toLocaleDateString('en-US', options);
        }

        document.addEventListener("DOMContentLoaded", function () {
            const greetingElement = document.getElementById("greeting");
            const dateElement = document.getElementById("date");

            const greeting = getGreeting();

            const formattedDate = formatDate();

            greetingElement.textContent = `${greeting}, <?php echo get_user_data($user_id)["username"]; ?>`;
            dateElement.textContent = formattedDate;
        });
    </script>

    <?php
} else {
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        // User is logged in, retrieve session data
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        header("Location: home.php");

    } else {
        // Redirect to the login page or perform other actions for non-logged-in users
        header("Location: login_register_form.php");
        exit();
    }
}
?>