# 💬 Chat-App Setup Guide

This project contains the complete source code for both **Chat-App** and **Chat-App-Admin**. Follow the steps below to set it up locally using XAMPP.

---

## 📁 Step 1: Move Folders to XAMPP Directory

Move the entire project folders to the following paths:

C:\xampp\htdocs\Chat-App
C:\xampp\htdocs\Chat-App-Admin

---

## ⚙️ Step 2: Update MySQL Configuration

Open the config file located at:

C:\xampp\htdocs\Chat-App\php\config.php


In the file, you will find the following line:

```php
$conn = mysqli_connect("localhost", "root", "", "chat");

If your MySQL has a password, update it like this:

$conn = mysqli_connect("localhost", "root", "YOUR_PASSWORD_HERE", "chat");

Replace YOUR_PASSWORD_HERE with your actual MySQL password.

🛠️ Step 3: Start Apache and MySQL
Open XAMPP Control Panel.

Start both Apache and MySQL modules.

🧱 Step 4: Create Database in phpMyAdmin
Open your browser and go to:

http://localhost/phpmyadmin

Create a new database named:

php-chatbox

Import the SQL file provided with this project (example: chat.sql) into the database:

Click on the php-chatbox database.

Go to the Import tab.

Choose the .sql file.

Click Go to import.

🚀 Step 5: Run the Chat App
Once the above steps are completed, open your browser and go to:

http://localhost/Chat-App

The Chat App should now be running successfully on your local machine.
