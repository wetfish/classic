FROM php:5.6-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
	curl \
	nginx \
	&& docker-php-ext-install mysqli mysql \
	&& curl -o /tmp/s6-overlay
	

RUN useradd fishy
USER fishy

COPY --chown=fishy:fishy . /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
