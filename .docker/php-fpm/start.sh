#!/usr/bin/env sh
set -e
role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then
    exec /usr/local/sbin/php-fpm
elif [ "$role" = "queue" ]; then
    echo "Running the queue..."
    php /var/www/artisan queue:work --verbose --tries=3 --timeout=90
elif [ "$role" = "cron" ]; then
    while [ true ]
    do
      php /var/www/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done
else
    echo "Could not match the container role \"$role\""
    exit 1
fi
