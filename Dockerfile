FROM php:8.3-apache

# Extensión necesaria para que PDO se conecte a MySQL/MariaDB
RUN docker-php-ext-install pdo_mysql

# Solo la carpeta public/ queda expuesta en el servidor web
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Los errores se registran, nunca se muestran al visitante
RUN printf "display_errors=Off\nlog_errors=On\nexpose_php=Off\n" > /usr/local/etc/php/conf.d/seguridad.ini
