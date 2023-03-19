#!/bin/sh

chown -R www-data:www-data /var/www/html/public/storage

php-fpm
