CREATE DATABASE teamtrack;

CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    profile_picture VARCHAR(255),
    role VARCHAR(50),
    background_color VARCHAR(7),
    about VARCHAR(1000)
);

CREATE TABLE Projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255),
    description VARCHAR(1000),
    start_date DATE,
    end_date DATE,
    status VARCHAR(50),
    background_color VARCHAR(7)
);

CREATE TABLE ProjectUsers (
    project_id INT,
    user_id INT,
    PRIMARY KEY (project_id, user_id),
    FOREIGN KEY (project_id) REFERENCES Projects(project_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    user_id INT,
    task_name VARCHAR(255),
    task_description VARCHAR(1000),
    start_date DATE,
    end_date DATE,
    status VARCHAR(50),
    section VARCHAR(50),
    priority INT,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    user_id INT,
    text VARCHAR(1000),
    timestamp TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES Tasks(task_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);
