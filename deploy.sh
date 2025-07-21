#!/bin/bash

# Laravel deployment script for DigitalOcean
echo "Running Laravel deployment script..."

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create storage symlink
php artisan storage:link

# Run migrations
php artisan migrate --force

# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment script completed!"
