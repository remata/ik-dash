FROM php:5.6-apache
MAINTAINER RM <renatas@renatas.lt>
RUN a2enmod rewrite
COPY html /var/www/html/
RUN chmod -R 755 /var/www
#COPY ./php.ini /usr/local/etc/php/
RUN docker-php-ext-install mysqli
