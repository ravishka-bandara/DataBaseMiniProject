# Gym Management System

A web-based gym management system for managing members, membership plans, trainers, attendance, and payments. The project uses HTML, CSS, and JavaScript for the frontend and PHP with MySQL for the backend API.

## Features

- Dashboard with totals for members, membership plans, trainers, and payments
- Member management:
  - Add, view, search, edit, and delete members
  - Store personal details, contact information, join date, membership plan, and trainer
  - Search members by name or phone number
- Membership plan management:
  - Add, view, search, edit, and delete plans
  - Store plan name, duration, price, and description
- Trainer management:
  - Add, view, search, edit, and delete trainers
  - Store trainer contact details and specialization
- Attendance management:
  - Record, view, search, edit, and delete attendance records
  - Store attendance date, check-in time, and check-out time
- Payment management:
  - Record, view, search, edit, and delete payments
  - Store payment date, amount, payment method, and description
  - Supports Cash, Card, and Bank Transfer
- Related records are connected through member, plan, and trainer IDs
- PHP APIs use prepared statements for database operations

## Technology Stack

- **Frontend:** HTML5, CSS3, vanilla JavaScript
- **Backend:** PHP
- **Database:** MySQL/MariaDB
- **Database driver:** PHP MySQLi
- **Data exchange:** JSON over HTTP

## Project Structure

```text
.
├── index.html             # Dashboard
├── members.html           # Members management page
├── plans.html             # Membership plans page
├── trainers.html          # Trainers page
├── attendance.html        # Attendance page
├── payments.html          # Payments page
├── css/
│   └── style.css          # Shared application styles
├── js/
│   ├── dashboard.js       # Dashboard counters
│   ├── members.js         # Member CRUD and search logic
│   ├── plans.js           # Plan CRUD and search logic
│   ├── trainers.js        # Trainer CRUD and search logic
│   ├── attendance.js      # Attendance CRUD and search logic
│   └── payments.js        # Payment CRUD and search logic
└── php/
    ├── db.php             # MySQL connection
    ├── dashboard.php      # Dashboard count API
    ├── members.php        # Member API
    ├── plans.php          # Membership plan API
    ├── trainers.php       # Trainer API
    ├── attendance.php     # Attendance API
    ├── payments.php       # Payment API
    └── test.php           # Backend test file
```

## Database Requirements

The backend connects to a local database using the following default configuration in `php/db.php`:

- **Host:** `localhost`
- **Username:** `root`
- **Password:** empty
- **Database:** `gym_management_system`

Create the database and the tables expected by the PHP files before running the application. The application expects these tables:

- `members`
- `membership_plans`
- `trainers`
- `attendance`
- `payments`

The tables should provide the IDs and fields used by the forms and PHP APIs, including relationships from members to membership plans and trainers, and from attendance and payments to members.

## Running Locally

1. Install a local PHP and MySQL environment such as [XAMPP](https://www.apachefriends.org/) or WAMP.
2. Start the Apache and MySQL services.
3. Clone or copy this repository into the web server document root, for example:

   ```text
   C:/xampp/htdocs/DataBaseMiniProject
   ```

4. Create a MySQL database named `gym_management_system` and add the required tables and columns.
5. If your MySQL credentials differ from the defaults, update `php/db.php`.
6. Open the application through the PHP server rather than directly from the filesystem:

   ```text
   http://localhost/DataBaseMiniProject/index.html
   ```

## How It Works

Each HTML page loads a shared stylesheet and its corresponding JavaScript module. The JavaScript modules send requests to the PHP endpoints in the `php/` directory. The PHP scripts read the `action` query parameter and return JSON responses for listing, searching, creating, updating, or deleting records.

For example, member requests use the following patterns:

```text
php/members.php?action=get&search=<term>
php/members.php?action=plans
php/members.php?action=trainers
php/members.php?action=add
php/members.php?action=update
php/members.php?action=delete&id=<member_id>
```

The dashboard calls `php/dashboard.php`, which returns counts from the members, membership plans, trainers, and payments tables.

## Notes

- Use the application through Apache/PHP so that the frontend can communicate with the PHP APIs.
- The repository currently contains the application code but does not include a database dump or SQL migration file; the MySQL schema must be created separately.
- Do not use production credentials in a local configuration file that is committed to the repository.
