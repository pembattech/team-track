<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Project</title>
    <!-- Add your CSS styles here -->
</head>

<body>
    <h1>Create a New Project</h1>
    <form action="partial/create_project.php" method="post">
        <label for="project_name">Project Name:</label>
        <input type="text" name="project_name" required><br>
        <label for="description">Description:</label>
        <textarea name="description" rows="4" required></textarea><br>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required><br>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" required><br>
        <label for="status">Status:</label>
        <input type="text" name="status" required><br>
        <label for="background_color">Background Color:</label>
        <input type="color" name="background_color" value="#ffffff"><br>
        <input type="submit" value="Create Project">
    </form>
</body>

</html>
