<style>
    .logo img {
        width: 60px;
        height: 60px;
        border: 1px solid var(--color-border);
        border-radius: 5px;
        background-color: var(--color-text);
    }

    .navbar-right img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
        object-fit: cover;

    }
</style>
<?php include 'base.php'; ?>
<div class="navbar">
    <div class="logo-container">
        <a href="home.php">
            <div class="logo collapse-toggle-btn">
                <img src="static/image/teamtrack_logo.png" alt="">
            </div>
        </a>
    </div>

    <a href="create_project_form.php">
        <div class="create-project-btn popup-btn overlay-border related-btn-img">
            <img class="svg-img" src="./static/image/add-square.svg" alt="create">
            <p>Create</p>
        </div>
    </a>

    <div class="search__container">
        <input class="search__input" type="text" placeholder="Search">
    </div>
    <div class="navbar-right">
        <?php
        // Start a session to access session variables (if needed)
        session_start();
        // Check if the user is logged in (assuming you have stored user_id in the session after login)
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            // Example usage: Get user data by user_id
            $user_data = get_user_data($user_id);
            if ($user_data !== null) {
                // Display profile picture or first letter of the full name
                display_profile_picture($user_id);
            } else {
                echo "User not found.";
            }
        } else {
            echo "User not logged in.";
        }

        // Function to display the profile picture or the first letter of the full name
        function display_profile_picture($user_id)
        {
            $user_data = get_user_data($user_id);
            $profile_picture = $user_data['profile_picture'];
            $full_name = $user_data['name'];

            if ($profile_picture !== null && $profile_picture !== "") {
                // Display the profile picture if it exists
                echo '<img id="popup-btn" src="' . $profile_picture . '" alt="Profile Picture">';
                // echo '<div class="profile-picture" id="popup-btn>';
                // echo '</div>';
            } else {
                // Display the initials of the full name as the profile picture
                $name_parts = explode(' ', $full_name);
                $initials = '';
                foreach ($name_parts as $part) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
                echo '<button id="popup-btn" style="background-color: ' . $user_data['background_color'] . ';">' . $initials . '</button>';
            }
        }
        ?>

        <!-- The popup menu -->
        <div class="popup-menu" id="myPopup">
            <a href="#" onclick="editProfile()">
                <p>Profile</p>
            </a>
            <a href="#" onclick="logout()">
                <p>Logout</p>
            </a>
        </div>
    </div>
</div>

<script>
    // Check if the session variable for project creation message exists
    var NotificationMessage = <?php echo isset($_SESSION['notification_message']) ? json_encode($_SESSION['notification_message']) : 'null'; ?>;
    if (NotificationMessage) {
        // Display the popup notification with the dynamic message
        var popupNotification = document.getElementById("popupNotification");
        popupNotification.innerText = NotificationMessage;
        popupNotification.style.display = "block";

        // Hide the popup after some time (e.g., 5 seconds)
        setTimeout(function () {
            popupNotification.style.display = "none";
        }, 5000); // 5000 milliseconds = 5 seconds

        // Clear the session variable to avoid showing the notification again on page refresh
        <?php unset($_SESSION['notification_message']); ?>
    }
</script>
<script src="static/js/main.js"></script>