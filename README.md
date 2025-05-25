# Gym Management System

A comprehensive gym management system with member registration, payment processing, attendance tracking, and support ticket functionality.

## Features

1. **Stored Procedures**
   - Member Registration
   - Payment Processing

2. **Triggers**
   - Prevent class overbooking
   - Verify payment amounts

3. **Support Ticket System**
   - User ticket creation and management
   - Admin interface for ticket resolution
   - MongoDB-based ticket storage

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- MongoDB 4.4 or higher
- XAMPP (or similar PHP development environment)
- Composer (for MongoDB PHP driver)

## Setup Instructions

1. **Database Setup**
   ```bash
   # Import MySQL database
   mysql -u root -p < CS306_GROUP_61_HW3_SQLDUMP.sql
   ```

2. **MongoDB Setup**
   ```bash
   # Start MongoDB service
   mongod --dbpath /path/to/data/directory
   ```

3. **Install MongoDB PHP Driver**
   ```bash
   # Navigate to project directory
   cd scripts/user
   composer require mongodb/mongodb

   cd ../admin
   composer require mongodb/mongodb
   ```

4. **Configure Database Connection**
   - Update MySQL credentials in `scripts/config/database.php`
   - Update MongoDB connection string if needed

5. **Web Server Setup**
   - Place the project in your web server's document root
   - Ensure proper permissions are set

## Project Structure

```
CS306_GROUP_61/
├── CS306_GROUP_61_HW3_SQLDUMP.sql
├── README.md
└── scripts/
    ├── config/
    │   └── .php
    ├── user/
    │   ├── index.php
    │   ├── sp_register_member.php
    │   ├── sp_add_payment.php
    │   ├── trigger_attendance.php
    │   ├── trigger_payment_check.php
    │   ├── support_index.php
    │   ├── support_create.php
    │   └── support_view.php
    └── admin/
        ├── index.php
        └── ticket_detail.php
```

## Usage

1. **User Interface**
   - Access `http://localhost/CS306_GROUP_61/scripts/user/`
   - Navigate through available features
   - Create and manage support tickets

2. **Admin Interface**
   - Access `http://localhost/CS306_GROUP_61/scripts/admin/`
   - View and manage support tickets
   - Add admin comments and resolve tickets

## Error Handling

- The system includes comprehensive error handling for:
  - Database connection issues
  - Invalid form submissions
  - Trigger violations
  - MongoDB operations

## Security Considerations

- All user inputs are sanitized
- SQL injection prevention
- XSS protection through proper escaping
- Secure MongoDB operations

## Contributing

This is a student project for CS306. Please contact the team for any questions or suggestions. 