FROM ubuntu:14.04

# Install dependencies

RUN apt-get update && apt-get install -y gcc make build-essential \
    libxml2-dev libcurl4-openssl-dev libpcre3-dev libbz2-dev libjpeg-dev \
    libpng12-dev libfreetype6-dev libt1-dev libmcrypt-dev libmhash-dev \
    freetds-dev libmysqlclient-dev unixodbc-dev libxslt1-dev wget


# Required to prevent an issue when compiling freetype support

RUN mkdir -pv /usr/include/freetype2/freetype \
    && ln -sf /usr/include/freetype2/freetype.h /usr/include/freetype2/freetype/freetype.h


# PHP sources & patches download

RUN wget -c -t 3 -O ./php-5.2.17.tar.gz http://museum.php.net/php5/php-5.2.17.tar.gz \
    && wget -c -t 3 -O ./libxml29_compat.patch https://mail.gnome.org/archives/xml/2012-August/txtbgxGXAvz4N.txt \
    && wget -c -t 3 -O ./debian_patches_disable_SSLv2_for_openssl_1_0_0.patch https://bugs.php.net/patch-display.php\?bug_id\=54736\&patch\=debian_patches_disable_SSLv2_for_openssl_1_0_0.patch\&revision=1305414559\&download\=1 \
    && wget -c -t 3 -O - http://php-fpm.org/downloads/php-5.2.17-fpm-0.5.14.diff.gz | gunzip > ./php-5.2.17-fpm-0.5.14.patch


# Patching PHP

RUN tar xvfz php-5.2.17.tar.gz \
    && cd php-5.2.17 \
    && patch -p0 -b < ../libxml29_compat.patch \
    && patch -p1 -b < ../debian_patches_disable_SSLv2_for_openssl_1_0_0.patch \
    && patch -p1 < ../php-5.2.17-fpm-0.5.14.patch

RUN apt-get install -y libapache2-mod-php5
RUN apt-get install -y php5-mysql
RUN apt-get install -y php5-mysqlnd
RUN apt-get install -y php5-gd


RUN php-5.2.17/configure \
    --prefix=/usr/share/php52 \
    --datadir=/usr/share/php52 \
    --mandir=/usr/share/man \
    --bindir=/usr/bin/php52 \
    --with-libdir=lib/x86_64-linux-gnu \
    --includedir=/usr/include \
    --with-config-file-path=$PHP_HOME/etc \
    --with-config-file-scan-dir=$PHP_HOME/etc/conf.d \
    --disable-debug \
    --with-regex=php \
    --disable-rpath \
    --disable-static \
    --disable-posix \
    --with-pic \
    --with-layout=GNU \
    --with-pear=/usr/share/php \
    --enable-calendar \
    --enable-sysvsem \
    --enable-sysvshm \
    --enable-sysvmsg \
    --enable-bcmath \
    --with-bz2 \
    --enable-ctype \
    --without-gdbm \
    --with-iconv \
    --enable-exif \
    --enable-ftp \
    --enable-cli \
    --with-gettext \
    --enable-mbstring \
    --with-pcre-regex \
    --enable-shmop \
    --enable-sockets \
    --enable-wddx \
    --enable-fastcgi \
    --enable-force-cgi-redirect \
    --enable-fpm \
    --with-mcrypt \
    --with-zlib \
    --enable-pdo \
    --with-curl \
    --enable-inline-optimization \
    --enable-xml \
    --enable-pcntl \
    --enable-mbregex \
    --with-mhash \
    --with-xsl \
    --enable-zip \
    --with-gd \
    --with-pdo-mysql \
    --with-jpeg-dir=/usr/lib \
    --with-png-dir=/usr/lib \
    --with-mysql \
    --with-openssl \
    --with-mysqli \
    --with-kerberos \
    --enable-dbase \
    --with-mysqli=/usr/bin/mysql_config \
    --enable-gd-native-ttf \
    --with-t1lib=/usr \
    --with-freetype-dir=/usr \
    --with-ldap \
    --with-kerberos=/usr \
    --with-unixODBC=shared,/usr \
    --with-imap-ssl \
    --with-mssql \
    --without-sqlite \
    --with-sqlite \
    --without-pdo-sqlite \
    --enable-soap \
    --with-pdo-sqlite \
    && make -j "$(nproc)" \
    && make install

RUN apt-get -yqq update && apt-get install -yqq apache2
RUN chmod -R 777 /var/www/html
RUN chmod -R 777 /var/lib/php
ADD ./font/arialbd.ttf /usr/share/fonts/truetype/
ADD ./font/arial.ttf /usr/share/fonts/truetype/

WORKDIR /var/www/html
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
RUN mkdir -p $APACHE_RUN_DIR $APACHE_LOCK_DIR $APACHE_LOG_DIR
ENTRYPOINT [ "/usr/sbin/apache2" ]
CMD ["-D", "FOREGROUND"]
EXPOSE 80
