FROM fishnet/php5.6-fpm-nginx-alpine

COPY src/package.json src/package-lock.json /var/www/src/

# get all the packages we need, build what we can't use from apk
RUN apk add --no-cache \
	git \
	nodejs \
	npm \
	libjpeg-turbo \
	libjpeg-turbo-dev \
	libpng \
	libpng-dev \
	freetype-dev \
	&& docker-php-ext-configure gd \
		--with-freetype-dir=/usr/include \
		--with-jpeg-dir=/usr/include \
		--with-png-dir=/usr/include \
	&& docker-php-ext-install -j$(nproc) mysqli mysql exif gd \
	&& apk del --no-cache --purge \
		libjpeg-turbo-dev \
		libpng-dev \
	&& rm -rf /tmp/* \
	&& addgroup -S -g 1000 fishy && adduser -u 1000 -S -G fishy fishy \
	&& mkdir -p /var/www/src/node_modules \
	&& npm install --prefix /var/www/src

COPY ./docker/wiki.conf /etc/nginx/locations.d/wiki.conf
COPY --chown=fishy:fishy . /var/www
COPY --chown=fishy:fishy ./docker/config.php /var/www/src/config.php
