FROM nginx:alpine

COPY .docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY .docker/nginx/conf.d/ /etc/nginx/conf.d/


RUN apk update \
    && apk upgrade \
    && apk add --no-cache openssl \
    && apk add --no-cache bash \
    && apk add --no-cache curl

COPY ./public/ /var/www/public/
COPY .docker/nginx/ssl /etc/nginx/ssl

ADD .docker/nginx/startup.sh /opt/startup.sh
RUN sed -i 's/\r//g' /opt/startup.sh
CMD ["/bin/bash", "/opt/startup.sh"]

WORKDIR /var/www

EXPOSE 80 81 443
