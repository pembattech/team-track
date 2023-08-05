

-- Insert dummy data into Users table
INSERT INTO Users (username, name, email, password, profile_picture, background_color, about)
VALUES
    ('pemba', 'Pemba Sherpa', 'pemba@example.com', 'password123', 'profile_pemba.jpg', '#e6f7ff', 'Hi, I am Pemba!'),
    ('rosham', 'Rosham Gupta', 'rosham@example.com', 'password456', 'profile_rosham.jpg', '#f2e5b4', 'Hello, I am Rosham.'),
    ('supriya', 'Supriya Patel', 'supriya@example.com', 'password789', 'profile_supriya.jpg', '#ffccdd', 'Hey there! I am Supriya.');

-- Insert dummy data into Projects table
INSERT INTO Projects (project_name, description, start_date, end_date, status, background_color)
VALUES
    ('Project A', 'This is the first project.', '2023-08-01', '2023-08-15', 'In Progress', '#c8e6c9'),
    ('Project B', 'A project for testing.', '2023-08-05', '2023-09-10', 'Not Started', '#f0f4c3');

-- Insert dummy data into ProjectUsers table
INSERT INTO ProjectUsers (project_id, user_id, is_projectowner, user_role)
VALUES
    (1, 1, 1, 'Project Owner'),
    (1, 5, 0, 'Developer'),
    (2, 1, 0, 'Developer'),
    (2, 3, 1, 'Project Owner');

-- Insert dummy data into Tasks table
INSERT INTO Tasks (project_id, user_id, task_name, assignee, task_description, start_date, end_date, status, section, priority)
VALUES
    (1, 1, 'Task 1', 2, 'Finish design phase.', '2023-08-01', '2023-08-05', 'In Progress', 'Planning', 'High'),
    (1, 2, 'Task 2', 2, 'Implement backend logic.', '2023-08-06', '2023-08-12', 'Not Started', 'Development', 'Medium'),
    (2, 1, 'Task 3', 3, 'Gather requirements.', '2023-08-05', '2023-08-10', 'Not Started', 'Planning', 'High');

-- Insert dummy data into Messages table
INSERT INTO Messages (task_id, user_id, text, timestamp)
VALUES
    (1, 1, 'Good progress!', '2023-08-02 09:15:00'),
    (1, 2, 'Keep it up!', '2023-08-03 14:30:00'),
    (2, 2, 'Let me know if you need help.', '2023-08-07 11:00:00');
