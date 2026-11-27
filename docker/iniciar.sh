#!/bin/sh
set -e
# Apache escucha en el puerto que asigne la plataforma.
PUERTO="${PORT:-10000}"
sed -i "s/^Listen .*/Listen ${PUERTO}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PUERTO}>/" /etc/apache2/sites-available/000-default.conf
exec apache2-foreground
