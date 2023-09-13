<style>
    .sidebar {
        width: 250px;
        color: white;
        height: 100vh;
        top: 65;
        overflow-y: auto;
        background-color: var(--sidebar-bgcolor);
        position: fixed;
    }

    .sidebar ul {
        list-style: none;
        padding: 10px 0;
    }

    .sidebar li {
        margin-bottom: 10px;
        padding: 3px 8px;
    }

    .sidebar a {
        border-radius: 5px;
        padding: 0 5px;
        text-decoration: none;
        font-size: 16px;
        color: var(--color-text);
    }

    .sidebar a.full-width {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--color-text);
        padding: 0 5px;
        font-size: 16px;
        width: 100%;
    }

    .sidebar a.full-width:hover {
        background-color: var(--color-background-hover);
    }

    .sidebar a:hover {
        background-color: var(--color-background-hover);
    }

    .sidebar a.active {
        background-color: var(--color-background-weak);
    }

    .sidebar .project-lst.active {
        background-color: var(--color-background-weak);
        border-radius: 5px;
    }

    .collapsible-project {
        cursor: pointer;
        padding: 0 5px;
    }

    .collapsible-content {
        display: block;
        padding-left: 5px;
    }

    .unread-badge {
        background-color: var(--danger-color);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        margin-left: 8px;
        transition: background-color 0.3s ease;
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
                <?php
                // Query to count unread messages for the user
                $sql = "SELECT COUNT(message_id) AS unread_count FROM Messages
                WHERE recipient_id = $user_id AND is_read = 0";

                $result = mysqli_query($connection, $sql);

                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $unreadCount = $row['unread_count'];

                    if ($unreadCount > 0) {
                        echo '<span class="unread-badge">' . $unreadCount . '</span>';
                    }
                } else {
                    echo 'Error: ' . mysqli_error($connection);
                }
                ?>
            </a>
        </li>
        <li>
            <div class="collapsible-project">Project ▼</div>
            <div class="collapsible-content">
                <div class="project-scroll-container">
                    <div class="project-item sidebar-project">
                        <?php
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
                                echo '    <p class="project-title">' . add_ellipsis($project_name, 23) . '</p>';
                                echo '</a>';
                                echo '</div>';
                            }
                        } else {
                            // If no projects are assigned, display a message or do something else
                            echo 'No projects.';
                        }

                        ?>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>
<script>
    // Function to remove active class from all links
    function removeAllActive() {
        const links = document.querySelectorAll('.sidebar a.full-width');
        links.forEach(link => {
            link.classList.remove('active');
        });
    }

    // Function to remove active class from all links
    function removeAllProjectLstActive() {
        const links = document.querySelectorAll('.sidebar project-link');
        links.forEach(link => {
            link.classList.remove('active');
        });
    }

    // Add active class based on current page URL
    const links = document.querySelectorAll('.sidebar a.full-width');
    const currentPageFileName = window.location.pathname.split('/').pop(); // Extract the filename
    console.log(currentPageFileName);
    removeAllActive();
    removeAllProjectLstActive();
    links.forEach(link => {
        console.log(link.getAttribute('href'));
        if (link.getAttribute('href') === currentPageFileName) {
            link.classList.add('active');
        }
    });

    // Add active class to the project list item based on the current project ID
    const projectLinks = document.querySelectorAll('.sidebar .project-link');
    const currentProjectID = <?php echo isset($_GET['project_id']) ? $_GET['project_id'] : 'null'; ?>;
    projectLinks.forEach(projectLink => {
        const projectID = projectLink.getAttribute('href').split('=')[1];
        if (projectID === String(currentProjectID)) {
            console.log(projectLink.closest('.project-lst'));
            projectLink.closest('.project-lst').classList.add('active');
        }
    });



    const collapsibles = document.querySelectorAll('.collapsible-project');
    collapsibles.forEach(collapsible => {
        collapsible.addEventListener('click', () => {
            const content = collapsible.nextElementSibling;
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        });
    });
</script>