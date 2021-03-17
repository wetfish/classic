FROM php:5.6-fpm-alpine

WORKDIR /var/www/src

# get all the packages we need, build what we can't use from apk
RUN apk add --no-cache \
	git \
	bash \
	nginx \
	nodejs \
	npm \
	libjpeg-turbo \
	libjpeg-turbo-dev \
	libpng-dev \
	libpng \
	freetype-dev \
	runit \
	&& docker-php-ext-configure gd \
		--with-freetype-dir=/usr/include \
		--with-jpeg-dir=/usr/include \
	&& docker-php-ext-install -j$(nproc) mysqli mysql exif gd \
	&& apk del --no-cache \
		libjpeg-turbo-dev \
		libpng-dev \
	&& rm -rf /tmp/* \
	&& addgroup -S -g 1000 fishy && adduser -u 1000 -S -G fishy fishy
	

# Set up our node links and our user
ADD ./config/php.conf /usr/local/etc/php-fpm.d/www.conf
ADD ./config/nginx.conf /etc/nginx/conf.d/default.conf
ADD ./config/sv/nginx/run /etc/service/nginx/run
ADD ./config/sv/php/run /etc/service/php/run

COPY --chown=fishy:fishy . /var/www
RUN mkdir -p /var/www/src/node_modules /run/nginx \
	&& chmod +x /etc/service/php/run /etc/service/nginx/run  \
	&& npm install --prefix /var/www/src

EXPOSE 8080
ENTRYPOINT ["/sbin/runsvdir", "/etc/service"]

