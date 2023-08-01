<?php include 'base.php'; ?>

<title>Access Denied</title>

<?php
// Start the session to access session data
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    echo $_SESSION['user_id'];
    ?>
    <!-- // User is not logged in, display the message and prevent further execution -->
    <!-- <div class="access-denied-container">
        <h1>Access Denied</h1>
        <div class="access-denied-content">
            <p>This page is exclusively accessible to logged-in users.</p>
            <p class="countdown">You will be automatically redirected to the login page in <span id="countdown">5</span>
                seconds.</p>
            <p>Alternatively, you can manually proceed to the login page by clicking on the "Login" link: <a
                    href="login_form.php">Login</a></p>
        </div>
    </div> -->
    <script>
        // Function to update the countdown timer and redirect after 5 seconds
        function updateTimer() {
            var countdownElement = document.getElementById("countdown");
            var countdown = parseInt(countdownElement.innerText);
            if (countdown <= 1) {
                window.location.href = "login_form.php"; // Replace "login_page.php" with your login page URL
            } else {
                countdown -= 1;
                countdownElement.innerText = countdown;
                setTimeout(updateTimer, 1000); // Update the timer every second (1000ms)
            }
        }

        // Start the countdown when the page loads
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(updateTimer, 1000); // Start the countdown after 1 second (1000ms)
        });
    </script>
    <?php
    exit();
}

// If the user is logged in, continue loading the protected page content
// Redirect to the login page or perform other actions for non-logged-in users
header("Location: home.php");
exit();
?>