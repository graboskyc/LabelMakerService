#!/bin/sh

#never run this anywhere other than local...
#openssl req -newkey rsa:2048 -nodes -keyout /etc/ssl/localhost.key -x509 -days 365 -out /etc/ssl/localhost.crt -subj "/C=US/ST=New Jersey/L=Lumberton/O=ATM Testing/OU=Software/CN=localhost"
#cat /etc/ssl/localhost.key /etc/ssl/localhost.crt > /etc/ssl/localhost.pem
exec nginx -g "daemon off;"
