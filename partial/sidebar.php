<div class="sidebar">
    <ul>
        <a href="home.php">
            <li class="sidebar-item related-btn-img"><img src="./static/image/home.svg" alt="">Home</li>
        </a>
        <a href="mytasks.php">
            <li class="sidebar-item related-btn-img"><img src="./static/image/check-square.svg" alt="">My
                Tasks</li>
        </a>
        <a href="inbox.php">
            <li class="sidebar-item related-btn-img"><img src="./static/image/bell.svg" alt="">Inbox</li>
        </a>
    </ul>
    <hr>
    <p class="heading">Project</p>

    <div class="project-item sidebar-project">
        <?php
        // Fetch project names from the "Projects" table
        $sql = "SELECT project_id, project_name FROM Projects";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            // Loop through the results and generate anchor tags for each project
            while ($row = $result->fetch_assoc()) {
                $project_id = $row['project_id'];
                $project_name = $row['project_name'];
                echo '<div class="project-lst">';
                echo '<a href="project.php?project_id=' . $project_id . '" class="project-link">';
                echo '    <div class="square"></div>';
                echo '    <p class="project-title">' . $project_name . '</p>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo '';
        }
        ?>
    </div>

</div>