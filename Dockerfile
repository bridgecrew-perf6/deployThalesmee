# Default docker hub as registry for image. This way, this can be overloaded.
ARG DOCKER_REGISTRY_URL
FROM ${DOCKER_REGISTRY_URL}php:5.3.29-apache

# Ajout des droits
# RUN chmod -R 777 /var/www/html
# RUN chmod -R 777 /var/lib/php

# Ajout des polices de caractères
ADD ./font/arialbd.ttf /usr/share/fonts/truetype/
ADD ./font/arial.ttf /usr/share/fonts/truetype/

# Copie des fichiers sources 
COPY ./conf/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./conf/php.ini /etc/php5/cli/php.ini
COPY ./conf/php.ini /etc/php5/apache/php.ini
COPY ./src/ /var/www/html

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