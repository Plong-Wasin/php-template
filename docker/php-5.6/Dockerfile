FROM php:5.6-fpm

RUN sed -i -e 's/deb.debian.org/archive.debian.org/g' \
    -e 's|security.debian.org|archive.debian.org/|g' \
    -e '/stretch-updates/d' /etc/apt/sources.list
# RUN cd / \
#     && wget ftp://ftp.freetds.org/pub/freetds/stable/freetds-1.3.3.tar.gz \
#     && tar -xzf freetds-1.3.3.tar.gz
# RUN cd /freetds-1.3.3 \
#     && ./configure --prefix=/usr/local --with-tdsver=7.4 --enable-msdblib --enable-sybase-compat \
#     && make \
#     && make install
RUN apt-get update && apt-get install -y --no-install-recommends \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && chmod 755 /var/www/html -R \
    && chown www-data:www-data /var/www/html \
    && apt update \
    && apt search vim \
    && apt install vim -y

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

# # Copy the php config file
# COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
# # ADD "https://www.random.org/cgi-bin/randbyte?nbytes=10&format=h" skipcache
# COPY ./docker/php/15-xdebug.ini /usr/local/etc/php/conf.d

# COPY ./docker/php/php.ini-development /usr/local/etc/php/conf.d/php.ini

# COPY ./docker/php/freetds.conf /etc/freetds/freetds.conf