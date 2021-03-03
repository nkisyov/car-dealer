FROM centos:7

# Install some must-haves
RUN yum -y install vim wget sendmail git unzip
RUN yum -y install libtool make automake autoconf nasm libpng-static

# Install PHP 7.2 on CentOS
RUN rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
	&& rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
RUN yum -y install yum-utils
RUN yum-config-manager --enable remi-php72

RUN yum -y install \
    php72w \
    php72w-bcmath \
    php72w-cli \
    php72w-common \
    php72w-curl \
    php72w-fpm \
    php72w-gd \
    php72w-ldap \
    php72w-imap \
    php72w-intl \
    php72w-mbstring \
    php72w-mcrypt \
    php72w-mysqlnd \
    php72w-opcache \
    php72w-pdo \
    php72w-pear \
    php72w-pecl-apcu \
    php72w-pecl-imagick \
    php72w-pgsql \
    php72w-process \
    php72w-pspell \
    php72w-recode \
    php72w-soap \
    php72w-tidy \
    php72w-xml

RUN php -v

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer
RUN composer --version

# Final update and clean up
RUN yum -y update
RUN yum clean all

# Prepare PHP environment
COPY config/php/php-fpm.conf /etc/php-fpm.conf
COPY config/php/www.conf /etc/php-fpm.d/www.conf
COPY config/php/php.ini /etc/php.d/php.ini
COPY config/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN git config --global user.email "root@localhost.com"
RUN git config --global user.name "Root User"

# Define work directory
WORKDIR /var/www/laravel

CMD ["php-fpm", "-F", "-O"]
