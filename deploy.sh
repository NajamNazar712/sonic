#!/bin/bash

echo "Pulling latest changes..."
git pull 
echo "Running migrations..."
php artisan migrate
echo "Running new seeders..."
SEEDERS=$(git diff --name-only HEAD@{1} HEAD | grep 'database/seeders/.*Seeder.php')

for seeder in $SEEDERS
do
  SEEDER_CLASS=$(basename "$seeder" .php)
  echo "Seeding: $SEEDER_CLASS"
  php artisan db:seed --class=$SEEDER_CLASS
done

echo "Deployment complete."

