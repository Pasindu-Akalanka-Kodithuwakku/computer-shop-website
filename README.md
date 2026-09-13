markdown

# Desktop Shop Website

This is a simple Computer Shop Management System created using PHP and MySQL.
---

## Tech Stack & Requirments
* **Local Server:** WAMP Server / XaMPP
* **Backend:** PHP (v8.x or higher)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript
---

## Installation & Setup Guide

### 1. Deploying the project to the local server
Copy or clone this project into the `www` directory of the WAMP Server (e.g., `C:\wamp64\www\`).

### 2. Setting up the database
1. Start the WAMP Server and go to `http://localhost/phpmyadmin`.
2. Create a new database (e.g., `my_db_name`).

### 3. Configuring Database Credentials
Open the file containing the project's database connection (`include\server\index.php`) and enter your local server details as shown below:
```php
$serverName = 'localhost';
$username = 'root';
$password = ''; // Since there is no default password for WAMP, leave it blank
$dbName = 'the_name_of_the_database you created';

### 4. Initial Admin Credentials
To log in to the system for the first time, create an admin account by running the following query via the `SQL` tab in phpMyAdmin:
```sql
INSERT INTO admin (adminId, firstName, contact, nic, password, answerOne, answerTwo) VALUES('AM0001', 'admin', 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', 'admin');

Default Mobile Number: admin
Default Nic Number: admin
Default Password: admin

### 5. Running the project
Open your web browser and go to the following URL:
http://localhost/[name_of_your_project_folder]
