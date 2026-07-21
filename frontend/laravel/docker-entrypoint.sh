#!/bin/sh
set -e

# Create SQLite database if it doesn't exist
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch database/database.sqlite
    echo "SQLite database initialized."
fi

# Generate app key
php artisan key:generate --force --quiet
echo "App key configured."

# Run migrations
php artisan migrate --force
echo "Migrations complete."

# Execute the main command (php artisan serve)
exec "$@"
