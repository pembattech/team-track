<div class="sidebar">
    <ul class="bottom-line" id="lst-link">
        <a href="home.php" id="link">
            <li class="sidebar-item related-btn-img"><img src="./static/image/home.svg" alt="">Home</li>
        </a>
        <a href="mytasks.php" id="link">
            <li class="sidebar-item related-btn-img"><img src="./static/image/check-square.svg" alt="">My
                Tasks</li>
        </a>
        <a href="inbox.php" id="link">
            <li class="sidebar-item related-btn-img"><img src="./static/image/bell.svg" alt="">Inbox</li>
        </a>
    </ul>
    <!-- Collapsible container for projects -->
    <div class="collapsible-container">
        <div class="collapsible-header">
            <ul>
                <li>
                    <p class="heading collapsible-icon">&#x25BC;</p> Projects
                </li>
            </ul>
        </div>
        <div class="project-scroll-container">
            <div class="project-item sidebar-project">
                <?php

                // Check if the user ID is set in the session
                if (isset($_SESSION['user_id'])) {
                    // Get the user ID of the logged-in user
                    $user_id = $_SESSION['user_id'];

                    // Fetch project names from the "Projects" table where the user is assigned
                    $sql = "SELECT P.project_id, P.project_name, P.background_color 
                        FROM Projects P
                        INNER JOIN ProjectUsers PU ON P.project_id = PU.project_id
                        WHERE PU.user_id = $user_id";

                    $result = $connection->query($sql);

                    if ($result->num_rows > 0) {
                        // Loop through the results and generate anchor tags for each project
                        while ($row = $result->fetch_assoc()) {
                            $project_id = $row['project_id'];
                            $project_name = $row['project_name'];
                            $background_color = $row['background_color'];
                            echo '<div class="project-lst">';
                            echo '<a href="project.php?project_id=' . $project_id . '" class="project-link" id="link">';
                            echo '    <div class="square" style="background-color:' . $background_color . '"></div>';
                            echo '    <p class="project-title">' . $project_name . '</p>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        // If no projects are assigned, display a message or do something else
                        echo 'No projects assigned to this user.';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle collapsible behavior -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get the collapsible header element
        const collapsibleHeader = document.querySelector(".collapsible-header");

        // Get the project item container element
        const projectContainer = document.querySelector(".project-scroll-container");

        // Set initial state for collapsible (collapsed)
        projectContainer.style.display = "none";

        // Toggle the display of projectContainer when collapsibleHeader is clicked
        collapsibleHeader.addEventListener("click", function () {
            if (projectContainer.style.display === "none") {
                projectContainer.style.display = "block";
                collapsibleHeader.querySelector(".collapsible-icon").innerHTML = "&#x25B2;"; // Up arrow
            } else {
                projectContainer.style.display = "none";
                collapsibleHeader.querySelector(".collapsible-icon").innerHTML = "&#x25BC;"; // Down arrow
            }
        });
    });
</script>