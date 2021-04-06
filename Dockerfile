FROM debian:buster

RUN apt-get update -y && apt-get install -y libpng-dev

RUN apt install -y curl wget gnupg2 ca-certificates lsb-release apt-transport-https
RUN wget https://packages.sury.org/php/apt.gpg
RUN apt-key add apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php7.list

## Install base packages
RUN apt-get update && \
    apt-get -yq install \
		apache2 \
		php7.2 \
		libapache2-mod-php7.2 \
		curl \
		ca-certificates \
		php7.2-zip \
		php7.2-json \
		php7.2-mysql \
		php7.2-gd \
		php7.2-xml \
		php7.2-xmlwriter \
		php7.2-mysqli \
		php7.2-mbstring \
		php7.2-calendar && \
	apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apt/archive/*.deb

# Ajout des droits
RUN chmod -R 777 /var/www/html

# Ajout des polices de caractères
ADD ./font/arialbd.ttf /usr/share/fonts/truetype/
ADD ./font/arial.ttf /usr/share/fonts/truetype/

# Copie des fichiers sources
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
