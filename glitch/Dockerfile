FROM php:5.6-fpm-stretch

WORKDIR /var/www

RUN groupadd -g 1000 fishy
RUN useradd -u 1000 -ms /bin/bash -g fishy fishy

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -\
    && apt-get update && apt-get install -y nodejs \
    && docker-php-ext-install mysqli mysql \ 
    && apt-get clean

COPY --chown=fishy:fishy . /var/www 

USER fishy
RUN npm install

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
