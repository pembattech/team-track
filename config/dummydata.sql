-- Delete records from ProjectUsers table associated with specified project names
DELETE PU
FROM ProjectUsers PU
INNER JOIN Projects P ON PU.project_id = P.project_id
WHERE P.project_name IN ('Project B', 'Project C', 'Project D', 'Project E', 'Project F', 'Project G', 'Project H');

-- Delete records from Projects table where project name starts with specified names
DELETE
FROM Projects
WHERE project_name IN ('Project B', 'Project C', 'Project D', 'Project E', 'Project F', 'Project G', 'Project H');


USE teamtrack;

-- Insert data into Projects table with project owner as user 1 (user 4 as the owner for Project D)
INSERT INTO Projects (project_id, project_name, description, start_date, end_date, priority, background_color)
VALUES
    (91, 'Project B', 'Description of Project B', '2023-11-15', '2023-12-15', 'Medium', '#3366FF'),
    (92, 'Project C', 'Description of Project C', '2023-12-01', '2024-02-28', 'Low', '#66FF33'),
    (93, 'Project D', 'Description of Project D', '2024-03-01', '2024-04-30', 'Medium', '#FF3366'),
    (94, 'Project E', 'Description of Project E', '2024-04-15', '2024-05-15', 'High', '#33FF66'),
    (95, 'Project F', 'Description of Project F', '2024-05-01', '2024-06-30', 'Low', '#6633FF'),
    (96, 'Project G', 'Description of Project G', '2024-06-15', '2024-07-15', 'Medium', '#FF6633'),
    (97, 'Project H', 'Description of Project H', '2024-07-01', '2024-08-31', 'High', '#33FF33');

-- Insert data into ProjectUsers table with project owner as user 1 (user 4 as the owner for Project D)
INSERT INTO ProjectUsers (projectuser_id, project_id, user_id, is_projectowner, user_role)
VALUES
    (92, 91, 1, 1, 'Member'),
    (93, 92, 1, 1, 'Owner'),
    (94, 93, 1, 1, 'Owner'),
    (95, 94, 1, 1, 'Owner'),
    (96, 95, 1, 1, 'Member'),
    (97, 96, 1, 1, 'Owner');
