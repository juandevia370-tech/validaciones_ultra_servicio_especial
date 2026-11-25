# Imagen de producción: PHP 8.2 + Apache
FROM php:8.2-apache

# Extensiones: pdo_pgsql (Supabase Postgres) y pdo_mysql (compatibilidad con MySQL).
# cURL y OpenSSL ya vienen incluidos en la imagen oficial.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql \
    && apt-get purge -y --auto-remove \
    && rm -rf /var/lib/apt/lists/*

# Configuración de PHP para producción
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php-produccion.ini "$PHP_INI_DIR/conf.d/zz-produccion.ini"

# Apache: raíz web en /public, mod_rewrite para el enrutador y cabeceras de seguridad
RUN a2enmod rewrite headers
COPY docker/apache-sitio.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache-seguridad.conf /etc/apache2/conf-available/zz-seguridad.conf
RUN a2enconf zz-seguridad

# Código de la aplicación (solo lo necesario; ver .dockerignore)
WORKDIR /var/www/html
COPY apps/ ./apps/
COPY config/ ./config/
COPY public/ ./public/
RUN chown -R root:root /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

COPY docker/iniciar.sh /usr/local/bin/iniciar.sh
RUN chmod 755 /usr/local/bin/iniciar.sh

# Render indica el puerto con la variable PORT (10000 por defecto)
ENV PORT=10000
EXPOSE 10000

CMD ["iniciar.sh"]
