    <?php include 'base.php'; ?>


    <div class="navbar">
        <button class="collapse-toggle-btn">M</button>

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

                if ($profile_picture !== null) {
                    // Display the profile picture if it exists
                    echo '<img src="' . $profile_picture . '" alt="Profile Picture">';
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
                <a href="#" onclick="editProfile()">Profile</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        </div>
    </div>


    <script src="static/js/main.js"></script>