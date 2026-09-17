FROM php:8.2-apache

# Enable Apache mod_rewrite
#RUN a2enmod rewrite

#instalar el pdo_mysql para php
RUN docker-php-ext-install pdo pdo_mysql

#copia los archivos html y php al contenedor
COPY . /var/www/html/

#cambia los permisos de las carpetas del servidor para que sean
#del usuario de apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

#RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

#expone el puerto 80 (http)
EXPOSE 80

#ejecuta apache 
CMD ["apache2-foreground"]
