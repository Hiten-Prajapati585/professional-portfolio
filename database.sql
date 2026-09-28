CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

CREATE TABLE admins (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE personal (
 id TINYINT UNSIGNED PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 role VARCHAR(150) DEFAULT '',
 tagline VARCHAR(255) DEFAULT '',
 about TEXT,
 email VARCHAR(190) DEFAULT '',
 phone VARCHAR(50) DEFAULT '',
 location VARCHAR(150) DEFAULT '',
 education_summary VARCHAR(255) DEFAULT '',
 languages VARCHAR(255) DEFAULT '',
 profile_photo VARCHAR(255) DEFAULT '',
 github VARCHAR(255) DEFAULT '',
 linkedin VARCHAR(255) DEFAULT '',
 instagram VARCHAR(255) DEFAULT '',
 facebook VARCHAR(255) DEFAULT '',
 twitter VARCHAR(255) DEFAULT '',
 canva_url VARCHAR(255) DEFAULT '',
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE skills (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 category VARCHAR(100) DEFAULT '',
 sort_order INT DEFAULT 0
);

CREATE TABLE education (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 degree VARCHAR(180) NOT NULL,
 institution VARCHAR(200) NOT NULL,
 location VARCHAR(150) DEFAULT '',
 start_date VARCHAR(50) DEFAULT '',
 end_date VARCHAR(50) DEFAULT '',
 grade VARCHAR(80) DEFAULT '',
 description TEXT,
 sort_order INT DEFAULT 0
);

CREATE TABLE experience (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 job_title VARCHAR(180) NOT NULL,
 company VARCHAR(200) NOT NULL,
 location VARCHAR(150) DEFAULT '',
 start_date VARCHAR(50) DEFAULT '',
 end_date VARCHAR(50) DEFAULT '',
 description TEXT,
 sort_order INT DEFAULT 0
);

CREATE TABLE projects (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(200) NOT NULL,
 description TEXT NOT NULL,
 category VARCHAR(100) DEFAULT '',
 technologies VARCHAR(500) DEFAULT '',
 github_url VARCHAR(255) DEFAULT '',
 live_url VARCHAR(255) DEFAULT '',
 start_date VARCHAR(50) DEFAULT '',
 completion_date VARCHAR(50) DEFAULT '',
 cover_image VARCHAR(255) DEFAULT '',
 pdf_file VARCHAR(255) DEFAULT '',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE project_images (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 project_id INT UNSIGNED NOT NULL,
 image_path VARCHAR(255) NOT NULL,
 FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE resumes (
 id TINYINT UNSIGNED PRIMARY KEY,
 file_path VARCHAR(255) DEFAULT '',
 file_name VARCHAR(255) DEFAULT '',
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO admins (email, password_hash) VALUES ('admin@example.com', '$2y$12$m5bzRpKtt03l0jeoAIM8Pe0NG1MPFkeI2It9kfF4w8/6mLENlR.TG');
INSERT INTO personal (id,name,role,tagline,about,email,phone,location,education_summary,languages) VALUES (1,'Your Name','Java Full Stack Developer','I build clean, responsive and user-friendly web applications.','Write your professional introduction here.','you@example.com','','India','','English, Hindi, Gujarati');
INSERT INTO resumes (id) VALUES (1);
INSERT INTO skills (name,category,sort_order) VALUES ('HTML5','Frontend',1),('CSS3','Frontend',2),('JavaScript','Frontend',3),('Bootstrap','Frontend',4),('Java','Backend',5),('PHP','Backend',6),('MySQL','Database',7),('Git & GitHub','Tools',8);
