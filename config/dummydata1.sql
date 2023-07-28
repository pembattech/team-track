
INSERT INTO Users (username, email, password, profile_picture, role) VALUES
    ('JohnDoe', 'john.doe@example.com', 'password123', 'profile1.jpg', 'Developer'),
    ('JaneSmith', 'jane.smith@example.com', 'password456', 'profile2.jpg', 'Project Manager'),
    ('MikeJohnson', 'mike.johnson@example.com', 'password789', 'profile3.jpg', 'Tester');


INSERT INTO Projects (project_name, description, start_date, end_date, status) VALUES
    ('TeamTrack Web App', 'Web application for project management', '2023-01-15', '2023-06-30', 'In Progress'),
    ('Mobile App Development', 'Mobile app development for Android and iOS', '2023-03-10', '2023-09-30', 'Planning'),
    ('Data Analysis Project', 'Analyzing data and generating reports', '2023-02-01', '2023-04-15', 'Completed');


INSERT INTO ProjectUsers (project_id, user_id) VALUES
    (1, 1), 
    (1, 2), 
    (2, 2), 
    (2, 3), 
    (3, 1); 


INSERT INTO Tasks (project_id, user_id, task_name, task_description, start_date, end_date, status, section, priority)
VALUES (1, 1, 'Task 1', 'Task 1 description goes here.', '2023-07-25', '2023-07-31', 'Incomplete', 'Doing', 1);

INSERT INTO Tasks (project_id, user_id, task_name, task_description, start_date, end_date, status, section, priority)
VALUES (1, 2, 'Task 2', 'Task 2 description goes here.', '2023-07-28', '2023-08-10', 'Incomplete', 'Doing', 2);

INSERT INTO Tasks (project_id, user_id, task_name, task_description, start_date, end_date, status, section, priority)
VALUES (2, 2, 'Task 3', 'Task 3 description goes here.', '2023-07-30', '2023-08-15', 'Incomplete', 'To Do', 3);

INSERT INTO Tasks (project_id, user_id, task_name, task_description, start_date, end_date, status, section, priority)
VALUES (2, 1, 'Task 4', 'Task 4 description goes here.', '2023-08-01', '2023-08-20', 'Completed', 'Done', 1);

INSERT INTO Tasks (project_id, user_id, task_name, task_description, start_date, end_date, status, section, priority)
VALUES (1, 1, 'Task 5', 'Task 5 description goes here.', '2023-08-05', '2023-08-15', 'Incomplete', 'Doing', 2);
INSERT INTO Tasks (project_id, user_id, task_name, task_description, start_date, end_date, status, section, priority)
VALUES (1, 7, 'Dummy Task10', 'This is a dummy task for user 7', '2023-07-23', '2023-07-30', 'Incomplete', 'To Do', 1);


INSERT INTO Messages (task_id, user_id, text, timestamp) VALUES
    (1, 1, 'The UI design is looking great!', '2023-02-05 10:30:00'),
    (1, 2, 'Thanks! I will start working on the backend API.', '2023-02-07 09:15:00'),
    (2, 3, 'I found a bug in the login API. I will fix it ASAP.', '2023-02-20 14:20:00'),
    (3, 1, 'I have started writing test cases for the frontend.', '2023-03-02 11:45:00'),
    (4, 2, 'The Android app UI is ready for testing.', '2023-03-25 08:00:00'),
    (4, 2, 'The Android app has been tested on multiple devices and works fine.', '2023-03-27 15:30:00'),
    (5, 3, 'I will start working on the iOS version today.', '2023-03-30 09:00:00'),
    (6, 1, 'Data analysis is complete, and the report is ready for review.', '2023-04-08 17:45:00');
