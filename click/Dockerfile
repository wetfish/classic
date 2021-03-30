FROM fishnet/php5.6-fpm-nginx-alpine

RUN docker-php-ext-install mysqli mysql \
	&& addgroup -S -g 1000 fishy && adduser -u 1000 -S -G fishy fishy

COPY ./docker/click.conf /etc/nginx/locations.d/click.conf 
COPY --chown=fishy:fishy . /var/www
