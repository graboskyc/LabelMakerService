FROM php:8.1-fpm

WORKDIR /var/www/html

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions curl

RUN mkdir -p /scripts
WORKDIR /scripts
COPY run.sh run.sh
RUN sed 's/\r$//' run.sh > run_lf.sh
RUN chmod +x ./run_lf.sh
ENTRYPOINT ./run_lf.sh
