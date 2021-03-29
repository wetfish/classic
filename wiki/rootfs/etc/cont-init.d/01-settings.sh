#!/usr/bin/with-contenv bash

DISPLAY_ERRORS=${DISPLAY_ERRORS:-On}

sed -e "s/@DISPLAY_ERRORS@/$DISPLAY_ERRORS/g" /usr/local/etc/php/conf.d/settings.ini
