FROM php:7.2-apache


RUN apt-get update -y && apt-get install -y libpng-dev

# Install curl php extension as we use it often
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install gd
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install zip
RUN docker-php-ext-install calendar

# Ajout des droits
RUN chmod -R 777 /var/www/html

# Ajout des polices de caractères
ADD ./font/arialbd.ttf /usr/share/fonts/truetype/
ADD ./font/arial.ttf /usr/share/fonts/truetype/

COPY ./conf/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./src/ /var/www/html
ADD ./conf/php.ini /usr/local/etc/php/


# Exposition du port 80
EXPOSE 80

# Répertoire par défaut dans le html
WORKDIR /var/www/html

# Ajout de var env 
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2

# Run de l'application
RUN mkdir -p $APACHE_RUN_DIR $APACHE_LOCK_DIR $APACHE_LOG_DIR
ENTRYPOINT [ "/usr/sbin/apache2" ]
CMD ["-D", "FOREGROUND"]
