ARG IMAGE_PHP_FPM_LATEST

FROM ${IMAGE_PHP_FPM_LATEST}

COPY . /var/www
COPY .env.prod /var/www/.env

RUN php /var/www/artisan key:generate \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

#TODO временно
#RUN php artisan optimize
