FROM alpine:3.9

RUN apk update
RUN export phpverx=$(alpinever=$(cat /etc/alpine-release|cut -d '.' -f1);[ $alpinever -ge 9 ] && echo  7|| echo 5)
RUN apk add apache2 php$phpverx-apache2
RUN export phpverx=$(alpinever=$(cat /etc/alpine-release);[ ${alpinever//./} -ge 309 ] && echo  7|| echo 5)

RUN apk add apache2 php$phpverx-apache2

RUN apk add php7-session php7-gd php7-mysqli php7-zlib php7-curl php7-mbstring php7-pdo_mysql php7-calendar php7-zip php7-json php7-xml php7-xmlwriter

COPY ./src/ /var/www/localhost/htdocs/
COPY ./conf/httpd.conf /etc/apache2/
COPY ./conf/php.ini /etc/php7/

# Ajout des polices de caractères
ADD ./font/arialbd.ttf /usr/share/fonts/truetype/
ADD ./font/arial.ttf /usr/share/fonts/truetype/

EXPOSE 80

CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
