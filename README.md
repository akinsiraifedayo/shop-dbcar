# Event Booking System

A simple PHP-based event booking system for managing users, events, and bookings.

## Requirements
- XAMPP or any PHP server with MySQL
- PHP version 7.4 or higher (tested with PHP 8.2.12)
- MariaDB or MySQL Database Server

---

## Installation

### Step 1: Download and Extract
1. Download the `.zip` file.
2. Extract the contents of the `.zip` file to your web server's root directory (`htdocs` for XAMPP).

### Step 2: Start XAMPP Services
1. Open XAMPP Control Panel.
2. Start the **Apache** and **MySQL** services.

### Step 3: Import the Database
1. Open your browser and navigate to `http://localhost/phpmyadmin`.
2. Create a new database named `event_management2`.
3. Click on the `Import` tab.
4. Select the file `event_management2.sql` located in the project folder.
5. Click `Go` to import the database.

### Step 4: Set Permissions
For Linux or macOS users, ensure the following directories have proper write permissions:
```bash
chmod 777 images
chmod 777 images/adult_photos
chmod 777 images/events
