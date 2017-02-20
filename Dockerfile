FROM php:7.1-apache
COPY php.ini /usr/local/etc/php/

RUN apt-get update && apt-get install -y curl \
  git \
  htop \
  nano \
  unzip \
  wget \
  mysql-client

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN pear channel-discover pear.phing.info && pear upgrade-all && pear install phing/phing
# Clean up APT
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo_mysql

RUN usermod -u 1000 www-data
RUN mkdir -p /project/web
RUN ln -s /project/web /var/www/project
RUN rm -v /etc/apache2/sites-available/000-default.conf
COPY docker/site.conf /etc/apache2/sites-available/000-default.conf

ENV TERMINFO=/opt/share/terminfo \
    TERM=xterm PHP_COMMAND=/usr/bin/php \
    DEBIAN_FRONTEND=noninteractive

WORKDIR /project
