-- Create the database and tables
CREATE DATABASE IF NOT EXISTS teamtrack;

USE teamtrack;

CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    profile_picture VARCHAR(255),
    background_color VARCHAR(7),
    about VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS Projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255),
    description VARCHAR(1000),
    start_date DATE,
    end_date DATE,
    priority VARCHAR(50),
    status VARCHAR(50),
    background_color VARCHAR(7),
    is_send_deadline_msg TINYINT(1) DEFAULT 0;
);

CREATE TABLE IF NOT EXISTS ProjectUsers (
    projectuser_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    user_id INT,
    is_projectowner INT DEFAULT 0,
    user_role VARCHAR(50),
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    projectuser_id INT,
    task_creator_id INT,
    task_name VARCHAR(255),
    assignee INT,
    task_description VARCHAR(1000),
    start_date DATE,
    end_date DATE,
    status VARCHAR(50),
    section VARCHAR(50),
    priority VARCHAR(50),
    is_send_deadline_msg TINYINT(1) DEFAULT 0;
    FOREIGN KEY (projectuser_id) REFERENCES ProjectUsers(projectuser_id) ON DELETE CASCADE,
    FOREIGN KEY (task_creator_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (assignee) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    sender_id INT,
    text VARCHAR(1000),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    recipient_id INT,
    is_read INT DEFAULT 0,
    is_task_msg INT DEFAULT 0,
    is_project_msg INT DEFAULT 0,
    is_newtask_msg INT DEFAULT 0,
    project_id INT,
    FOREIGN KEY (task_id) REFERENCES Tasks(task_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ProjectInvitations (
    invitation_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    email VARCHAR(255),
    otp VARCHAR(10),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_used TINYINT(1) DEFAULT 0,
    invitation_sender INT,
    FOREIGN KEY (invitation_sender) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS RecentActivity (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity_type VARCHAR(255),
    activity_description TEXT,
    activity_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    project_id INT,
    task_id INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES Tasks(task_id) ON DELETE CASCADE
);

