FROM php:8.2-fpm-alpine

RUN apk update \
    && pecl channel-update pecl.php.net \
    && apk add $PHPIZE_DEPS \
    && docker-php-ext-install \
        bcmath \
        exif \
        opcache

###########################################################################
# ZIP:
###########################################################################
ARG INSTALL_ZIP=true

RUN if [ ${INSTALL_ZIP} = true ]; then \
    apk add \
        zip \
        unzip \
        libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
;fi

###########################################################################
# GD:
###########################################################################
ARG INSTALL_GD=true

RUN if [ ${INSTALL_GD} = true ]; then \
    apk add \
        libpng-dev \
        libwebp-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd \
;fi

###########################################################################
# Nodejs & NPM:
###########################################################################
ARG INSTALL_NODE=true

RUN if [ ${INSTALL_NODE} = true ]; then \
    apk add --no-cache \
        --repository https://dl-cdn.alpinelinux.org/alpine/v3.17/main \
        nodejs=18.14.2-r0  \
        npm=9.1.2-r0 \
;fi

###########################################################################
# Image optimizers:
###########################################################################
ARG INSTALL_IMAGE_OPTIMIZERS=true

RUN if [ ${INSTALL_IMAGE_OPTIMIZERS} = true ]; then \
    apk add jpegoptim optipng pngquant gifsicle \
    && npm install -g svgo \
;fi

###########################################################################
# ImageMagick:
###########################################################################
ARG INSTALL_IMAGEMAGICK=true

RUN if [ ${INSTALL_IMAGEMAGICK} = true ]; then \
    apk add imagemagick imagemagick-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
;fi

###########################################################################
# Composer:
###########################################################################
ARG INSTALL_COMPOSER=true

RUN if [ ${INSTALL_COMPOSER} = true ]; then \
    curl -sS https://getcomposer.org/installer | php -- \
            --install-dir=/usr/local/bin \
            --filename=composer \
    && chmod +x /usr/local/bin/composer \
;fi

###########################################################################
# Redis:
###########################################################################
ARG INSTALL_REDIS=true

RUN if [ ${INSTALL_REDIS} = true ]; then \
    apk add --no-cache pcre-dev \
        && pecl install redis \
        && docker-php-ext-enable redis.so \
;fi

###########################################################################
# MySQL Client:
###########################################################################
ARG INSTALL_MYSQL_CLIENT=false

RUN if [ ${INSTALL_MYSQL_CLIENT} = true ]; then \
    apk add mysql-client \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
;fi


###########################################################################
# Postgres:
###########################################################################
ARG INSTALL_POSTGRES=true

RUN if [ ${INSTALL_POSTGRES} = true ]; then \
    apk add postgresql-dev \
        && docker-php-ext-install pdo pdo_pgsql \
;fi

###########################################################################
# xDebug:
###########################################################################
ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-3.1.2; \
    docker-php-ext-enable xdebug \
;fi
# Copy xdebug configuration for remote debugging
COPY .docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

###########################################################################
# XHProf: Doc https://sites.google.com/a/future-group.ru/instructions/%D0%B4%D0%BE%D0%BB%D0%B6%D0%BD%D0%BE%D1%81%D1%82%D0%BD%D1%8B%D0%B5-%D0%B8%D0%BD%D1%81%D1%82%D1%80%D1%83%D0%BA%D1%86%D0%B8%D0%B8/%D0%B8%D0%BD%D1%81%D1%82%D1%80%D1%83%D0%BA%D1%86%D0%B8%D0%B8-%D0%B4%D0%BB%D1%8F-backend-%D0%BE%D1%82%D0%B4%D0%B5%D0%BB%D0%B0/2-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%B0-%D1%81-%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B0%D0%BC%D0%B8/debugging-%D0%BF%D1%80%D0%BE%D1%84%D0%B8%D0%BB%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5/xhprof
###########################################################################
ARG INSTALL_XHPROF=false

WORKDIR /var/tmp
RUN if [ ${INSTALL_XHPROF} = true ]; then \
    apk add git \
        && git clone https://github.com/tideways/php-xhprof-extension.git \
        && cd php-xhprof-extension \
        && phpize \
        && ./configure \
        && make \
        && make install \
        && echo 'extension=tideways_xhprof.so' >> /usr/local/etc/php/conf.d/tideways_xhprof.ini \
        && apk del git && rm -rf /var/tmp/* \
;fi


###########################################################################


#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------

COPY .docker/php-fpm/php8.ini /usr/local/etc/php/php.ini
COPY .docker/php-fpm/laravel.ini /usr/local/etc/php/conf.d
COPY .docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY .docker/php-fpm/xlaravel.pool.conf /usr/local/etc/php-fpm.d/xlaravel.pool.conf
COPY .docker/php-fpm/policy.xml /etc/ImageMagick-6/policy.xml
COPY .docker/php-fpm/start.sh /usr/local/sbin/start



# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN apk add shadow \
    && rm -rf /var/www/html \
    && groupmod -o -g ${PGID} www-data \
    && usermod -o -u ${PUID} -g www-data www-data \
    && chmod u+x /usr/local/sbin/start

CMD ["/usr/local/sbin/start"]

WORKDIR /var/www
