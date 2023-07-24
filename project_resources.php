<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>
    <?php
    // Function to sanitize input to prevent SQL injection
    function sanitize_input($input)
    {
        global $connection;
        return mysqli_real_escape_string($connection, $input);
    }

    // Check if the 'project_id' parameter is present in the URL
    if (isset($_GET['project_id'])) {
        // Sanitize the input to prevent SQL injection
        $project_id = sanitize_input($_GET['project_id']);

        // Query to fetch project details from the 'Projects' table
        $sql_project = "SELECT * FROM Projects WHERE project_id = $project_id";
        $result_project = mysqli_query($connection, $sql_project);

        if (mysqli_num_rows($result_project) > 0) {
            $project = mysqli_fetch_assoc($result_project);

            echo "<div class='main-content'>";
            echo "<div class='heading-content'>";
            echo "<div class='heading-style'>";
            echo "<p>Project Name: " . $project['project_name'] . "</p>";
            echo "<p>Description: " . $project['description'] . "</p>";
            echo "<p>Start Date: " . $project['start_date'] . "</p>";
            echo "<p>End Date: " . $project['end_date'] . "</p>";
            echo "<p>Status: " . $project['status'] . "</p>";
            echo "</div>";
            echo "</div>";

            // Query to fetch users associated with the project from the 'ProjectUsers' table
            $sql_users = "SELECT Users.* FROM Users
                      INNER JOIN ProjectUsers ON Users.user_id = ProjectUsers.user_id
                      WHERE ProjectUsers.project_id = $project_id";
            $result_users = mysqli_query($connection, $sql_users);

        } else {
            echo "Project not found.";
        }
    } else {
        echo "Invalid project ID.";
    }
    ?>

    <?php
    if (mysqli_num_rows($result_users) > 0) {
        echo "<div class='users-list'>";
        echo "<h2>Users associated with this project:</h2>";
        echo "<ul>";
        while ($user = mysqli_fetch_assoc($result_users)) {
            echo "<li>" . $user['username'] . " (" . $user['email'] . ")</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<p>No users associated with this project.</p>";
    }
    ?>
    <script>
        function toggleCollapse(className) {
            var rows = document.getElementsByClassName(className);
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                if (row.style.display === "none") {
                    row.style.display = "table-row";
                } else {
                    row.style.display = "none";
                }
            }
        }

        var initialCollapsedRows = document.getElementsByClassName('collapsed');
        for (var i = 0; i < initialCollapsedRows.length; i++) {
            initialCollapsedRows[i].style.display = "table-row";
        }
    </script>