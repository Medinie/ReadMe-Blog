**ReadMe Blog Application**

A lightweight Blog Platform built with PHP, MySQL, and JavaScript, featuring user authentication, post management, and responsive design.

* Features 

--> Secure user registration & login
--> Create, edit, and delete blog posts
--> Upload cover images
--> Responsive modern UI
--> SQL injection & XSS protection
--> User-based access control

* Requirements

 XAMPP  local server
   --> PHP 
   --> MySQL 
   --> Apache Web Server

* Installation (Localhost)

1. Setup Project
--> Copy the project folder to your XAMPP htdocs directory.
--> Start Apache and MySQL in XAMPP.

2. Create Database
--> Open http://localhost/phpmyadmin
--> Create a database named blog_app

3. Run this SQL code

4. Configure Database
 Edit .env or config.php:

   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=blog_app

5. Run the App

* Folder Structure

ReadMe-blog/
│
├── css/             # Styling files
├── uploads/         # Uploaded images
├── config.php       # Database connection
├── index.php        # Homepage
├── create.php       # Add blog
├── edit.php         # Edit blog
├── delete.php       # Delete blog
├── login.php        # User login
├── register.php     # Registration
|── .env             # Configuration file
|── README.md        # Explaining Project

* Usage

--> Register → Create a new account
--> Login → Access your dashboard
--> Create Blog → Add title, image, and content
--> Edit/Delete → Manage your own posts

* Security

--> Password hashing (password_hash)
--> Prepared statements (SQL injection protection)
--> XSS protection (htmlspecialchars)
--> File upload validation

* Technologies Used

--> Frontend: HTML, CSS, JavaScript
--> Backend: PHP (MySQLi)
--> Database: MySQL
--> Server: XAMPP / InfinityFree (Apache)

* Hosting on InfinityFree

 You can host this project online for free using InfinityFree.

 Steps:
  --> Create Account → Sign up at infinityfree.net
  --> Create Hosting Account → Choose a subdomain like myblog.epizy.com
  --> Upload Files
       > Open File Manager → go to /htdocs/
       > Delete the default file and upload all your project files
  --> Create MySQL Database
       > Go to MySQL Databases → create a database
       > Note down: DB name, username, password, hostname
       > Import Tables
  --> Open phpMyAdmin → select your database
       > Run the same SQL code for user and blogPost tables
  --> Update config.php

         $host = "sqlXXX.infinityfree.com"; // From cPanel
         $user = "epiz_12345678";
         $pass = "your_db_password";
         $dbname = "epiz_12345678_blogapp";
         $conn = mysqli_connect($host, $user, $pass, $dbname);


* Test Live Website
Visit: https://readmeblogs.infinityfreeapp.com/


