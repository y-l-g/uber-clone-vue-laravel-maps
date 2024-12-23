#!/bin/sh
set -e

mv .env.example .env

for secret_file in /run/secrets/*; do
    name=$(basename $secret_file | sed "s/^${APP_NAME}_//")
    value=$(cat $secret_file)
    sed -i "s|^${name}=.*|${name}=${value}|" /app/.env
done

npm run build

exec "$@"
