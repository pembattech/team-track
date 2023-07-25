<?php include 'partial/navbar.php'; ?>
<div class="container">
    <?php include 'partial/sidebar.php'; ?>
    <div class="main-content">
        <div class="heading-content">
            <div class="heading-style">
                <p>Create a New Project</p>
            </div>
            <form action="partial/create_project.php" method="POST">
                <label for="project_name">Project Name:</label>
                <input type="text" name="project_name" required><br>
                <label for="description">Description:</label>
                <textarea name="description" rows="4" required></textarea><br>
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" required><br>
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" required><br>
                <input type="submit" value="Create Project">
            </form>
        </div>
    </div>
</div>