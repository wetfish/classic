FROM php:5.6-fpm-stretch

WORKDIR /var/www/src

RUN apt-get update && apt-get install -y \
    apt-utils \
    git \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/freetype2/ \ 
    && docker-php-ext-install mysqli mysql exif gd

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -\
    && apt-get install -y nodejs

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
