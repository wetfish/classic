FROM fishnet/php5.6-fpm-nginx-alpine

RUN apk add --no-cache \
	nodejs \
	npm \
	&& rm -rf /tmp/* \
	&& addgroup -S -g 1000 fishy && adduser -u 1000 -S -G fishy fishy

COPY ./docker/glitch.conf /etc/nginx/locations.d/glitch.conf
COPY --chown=fishy:fishy . /var/www 

RUN mkdir -p /var/www/node_modules \
	&& npm install
