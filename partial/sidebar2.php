<style>
    .sidebar {
        width: 250px;
        background-color: #333;
        color: white;
        height: 100vh;
        top: 0;
        left: 0;
        overflow-y: auto;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar li {
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        text-decoration: none;
        font-size: 18px;
        color: #fff;
        background-color: blue;
        transition: background-color 0.3s ease;
    }

    .sidebar a.full-width {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #fff;
        background-color: blue;
        transition: background-color 0.3s ease;
        padding: 10px;
        font-size: 18px;
        width: 100%;
    }

    .sidebar a.full-width:hover {
        background-color: red;
    }

    .sidebar a:hover {
        background-color: red;
    }

    .sidebar a.active {
        background-color: #555;
    }

    .collapsible {
        cursor: pointer;
    }

    .collapsible-content {
        display: none;
        padding-left: 20px;
    }

    /* Additional styles to fix collapsible issue */
    .collapsible-content a {
        display: block;
    }
</style>
<nav class="sidebar">
    <ul>
        <li class="related-btn-img">
            <a href="home.php" class="full-width">
                <img src="./static/image/home.svg" alt="">
                Home
            </a>
        </li>
        <li class="related-btn-img">
            <a href="mytasks.php" class="full-width">
                <img src="./static/image/check-square.svg">
                My Task
            </a>
        </li>
        <li class="related-btn-img">
            <a href="inbox.php" class="full-width">
                <img src="./static/image/bell.svg" alt="">
                Inbox
            </a>
        </li>
        <li>
            <div class="collapsible project">Project ▼</div>
            <div class="collapsible-content">
                <div class="project-scroll-container">
                    <div class="project-item sidebar-project">
                        <?php
                        // Check if the user ID is set in the session
                        
                        // Get the user ID of the logged-in user
                        $user_id = 1;

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

                        ?>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>
<script>
    // const links = document.querySelectorAll('.sidebar a.full-width');

    // links.forEach(link => {
    //     link.addEventListener('click', () => {
    //         links.forEach(otherLink => otherLink.classList.remove('active'));
    //         link.classList.add('active');
    //     });
    // });

    // Function to remove active class from all links
    function removeAllActive() {
        const links = document.querySelectorAll('.sidebar a.full-width');
        links.forEach(link => {
            link.classList.remove('active');
        });
    }

    // Add active class based on current page URL
    const links = document.querySelectorAll('.sidebar a.full-width');
    const currentPageFileName = window.location.pathname.split('/').pop(); // Extract the filename

    links.forEach(link => {
        console.log(link.getAttribute('href'));
        console.log(window.location.pathname)
        console.log(currentPageFileName)
        if (link.getAttribute('href') === currentPageFileName) {
            link.classList.add('active');
        }
    });

    const collapsibles = document.querySelectorAll('.collapsible');

    collapsibles.forEach(collapsible => {
        collapsible.addEventListener('click', () => {
            const content = collapsible.nextElementSibling;
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        });
    });
</script>