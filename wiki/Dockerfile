FROM php:5.6-fpm-alpine

WORKDIR /var/www

# get all the packages we need, build what we can't use from apk
RUN apk add --no-cache \
	bash \
	curl \
	git \
	nginx \
	nodejs \
	npm \
	libjpeg-turbo \
	libjpeg-turbo-dev \
	libpng \
	libpng-dev \
	freetype-dev \
        && curl -o /tmp/s6-overlay-amd64.tar.gz -jkSL https://github.com/just-containers/s6-overlay/releases/download/v2.2.0.1/s6-overlay-amd64.tar.gz \
	&& tar xvfz /tmp/s6-overlay-amd64.tar.gz -C / \
	&& docker-php-ext-configure gd \
		--with-freetype-dir=/usr/include \
		--with-jpeg-dir=/usr/include \
		--with-png-dir=/usr/include \
	&& docker-php-ext-install -j$(nproc) mysqli mysql exif gd \
	&& apk del --no-cache --purge \
		libjpeg-turbo-dev \
		libpng-dev \
		curl \
	&& rm -rf /tmp/* \
	&& addgroup -S -g 1000 fishy && adduser -u 1000 -S -G fishy fishy
	
# Copy files and update perms where necessary
COPY --chown=fishy:fishy . /var/www
COPY ./config/services.d/ /etc/services.d
ADD ./config/php.conf /usr/local/etc/php-fpm.conf
ADD ./config/nginx.conf /etc/nginx/conf.d/default.conf
ADD ./config/config.php /var/www/src/config.php


# Set up some dirs and ownerships, then fetch our node packages
RUN mkdir -p /var/www/src/node_modules /run/nginx /usr/local/var/log \
	&& chown -R fishy:fishy /usr/local/var/log /var/www/src/config.php \
	&& npm install --prefix /var/www/src

EXPOSE 8080
ENTRYPOINT ["/init"]
