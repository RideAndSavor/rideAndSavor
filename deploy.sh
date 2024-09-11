#!/bin/bash

# Change to the project directory
cd /path/to/your/project

# Pull the latest code from Git
git pull origin master

# Install/update dependencies
composer install

# Run database migrations
php artisan migrate
