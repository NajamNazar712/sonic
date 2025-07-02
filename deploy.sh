#!/bin/bash

echo "Artisan Down..."
php artisan down
echo "Pulling latest changes..."
git pull
echo "Running migrations..."
php artisan migrate --force
echo "Running new seeders..."
SEEDERS=$(git diff --name-only HEAD@{1} HEAD | grep '^database/seeders/.*\.php$')

for seeder in $SEEDERS
do
  SEEDER_CLASS=$(basename "$seeder" .php)
  echo "Seeding: $SEEDER_CLASS"
  php artisan db:seed --class=$SEEDER_CLASS --force
done

echo "Artisan Up..."
php artisan up
echo "Deployment complete."

