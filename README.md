# FoodShare - Food Waste Redistribution Management System

## 1. Project Overview
FoodShare is a web-based Food Waste Redistribution Management System developed using PHP and MySQL. The system helps connect food donors with NGOs or charities so that available food can be collected and distributed to beneficiaries.

## 2. Project Objectives
- Reduce food waste.
- Connect donors with NGOs and charities.
- Manage food donations and collection requests.
- Support food distribution to beneficiaries.
- Store and manage project data using MySQL.

## 3. Technologies Used
- PHP
- MySQL
- HTML
- CSS
- JavaScript
- XAMPP
- phpMyAdmin
- Visual Studio Code

## 4. Project Workflow
Donor → Food Donation → NGO/Charity → Collection → Distribution → Beneficiary

## 5. Main Features
- User registration and login
- Donor dashboard
- Add and manage food donations
- NGO dashboard
- Collection request management
- Beneficiary management
- Food distribution records
- MySQL database integration

## 6. Database
Database name: `food_waste_db`

Database file: `food_waste_db.sql`

The SQL file can be imported through phpMyAdmin to create the database tables. Check the SQL file before importing it.

## 7. Installation Instructions
1. Install XAMPP on your computer.
2. Copy the project folder into `C:\xampp\htdocs\food_waste`.
3. Start Apache and MySQL from XAMPP Control Panel.
4. Open `http://localhost/phpmyadmin`.
5. Create a database named `food_waste_db`.
6. Select the database and import `food_waste_db.sql`.
7. Configure the database connection using your local MySQL settings.
8. Open `http://localhost/food_waste/` in your browser.

## 8. Security
- Do not publish passwords or database credentials.
- Do not upload real users' private information.
- Store passwords securely using PHP password hashing.
- Use CSRF protection on forms that modify data.

## 9. Academic Purpose
This project is developed for university academic purposes to demonstrate a PHP and MySQL-based food waste redistribution system.

## 10. Author
Student Name: [Aysha Baktar Joty]
ID: [2024000010142]

