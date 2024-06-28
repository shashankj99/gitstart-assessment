#!/bin/sh
set -e

echo "Waiting for the database to be ready..."
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  sleep 1
done

php bin/console doctrine:migrations:migrate --no-interaction

# generate key pair for jwt authentication
php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

php bin/console doctrine:database:create --env=test

php bin/console doctrine:migrations:migrate --env=test --no-interaction

exec "$@"
