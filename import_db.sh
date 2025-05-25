#!/bin/bash

# Wait for MySQL to be ready
while ! /Applications/XAMPP/xamppfiles/bin/mysql -u root -e "SELECT 1" >/dev/null 2>&1; do
    echo "Waiting for MySQL to be ready..."
    sleep 1
done

# Drop and recreate the database
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "DROP DATABASE IF EXISTS GymDB; CREATE DATABASE GymDB;"
/Applications/XAMPP/xamppfiles/bin/mysql -u root GymDB < CS306_GROUP_61_HW3_SQLDUMP.sql

echo "Database imported successfully!" 