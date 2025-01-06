Car Repair Website (Sheffield Auto Parts)

Overview
This project is an Inventory Management System for a car repair business. The website is designed to help manage the car parts inventory, repair schedules, customer data, and staff involvement. The system is built with PHP, CSS, JavaScript, and SQL.

Each team member will have a section to add and manage their details within the system. The project aims to allow easy management of car repair operations and inventory control.

Features
Admin Panel: The admin can manage users, view inventory data, and control system access.
Inventory Management: The ability to manage and track car parts inventory, including adding new parts, updating stock levels, and reordering parts.
Car Repair Scheduling: Users can schedule car repair services and track their progress.
User Profiles: Team members can add and manage their personal details, such as roles and responsibilities.
Search and Filter: A search bar and filters for easy inventory and user searches.
CSV Export: Admin can export inventory data to CSV format for reporting.

Usage
Admin Login
Username: admin
Password: password123
The admin will have access to all functionalities, including managing users and inventory.


Sure! Below is a README.md template for your Car Repair Website (Inventory Management System) project. You can adjust the details as per your actual project and team.

Car Repair Website (Inventory Management System)
Overview
This project is an Inventory Management System for a car repair business. The website is designed to help manage the car parts inventory, repair schedules, customer data, and staff involvement. The system is built with PHP, CSS, JavaScript, and SQL.

Each team member will have a section to add and manage their details within the system. The project aims to allow easy management of car repair operations and inventory control.

Features
Admin Panel: The admin can manage users, view inventory data, and control system access.
Inventory Management: The ability to manage and track car parts inventory, including adding new parts, updating stock levels, and reordering parts.
Car Repair Scheduling: Users can schedule car repair services and track their progress.
User Profiles: Team members can add and manage their personal details, such as roles and responsibilities.
Search and Filter: A search bar and filters for easy inventory and user searches.
CSV Export: Admin can export inventory data to CSV format for reporting.
Installation
Prerequisites
Before setting up this project, you need the following:

A web server like Apache or Nginx
PHP (preferably PHP 7.x or higher)
MySQL or MariaDB for the database
A text editor (e.g., VSCode, Sublime Text, etc.)
Steps
Clone this repository:

bash
Copy code
git clone https://github.com/yourusername/car-repair-website.git
cd car-repair-website
Create a MySQL database:

You can create a database using a tool like phpMyAdmin or via the MySQL command line.

Example command to create a database:

sql
Copy code
CREATE DATABASE car_repair_db;
Import the provided SQL file (found in the database/ folder) to create the necessary tables:

bash
Copy code
source /path/to/database/setup.sql;
Update the include/init.php file with your database credentials (hostname, username, password, and database name):

php
Copy code
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'car_repair_db';

$con = new mysqli($host, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
Open your web browser and navigate to the project directory (e.g., http://localhost/car-repair-website) to view the application.

Usage
Admin Login
Username: admin
Password: password123
The admin will have access to all functionalities, including managing users and inventory.

Managing Inventory
Add new parts to the inventory by navigating to the "Inventory Management" section.
View and search for parts based on the quantity in stock and reorder level.

Export Data to CSV
Admin users can export inventory data to CSV format by clicking the "Export" button available in the inventory section.
