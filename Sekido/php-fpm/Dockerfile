FROM debian:buster-slim

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    apt-transport-https \
    ca-certificates \
    curl \
    git \
    php7.3 \
    php7.3-fpm \
    php7.3-mbstring \
    php7.3-mysql \
    php7.3-xml \
    php7.3-zip \
    php-oauth \
    unzip \
    zlib1g-dev \
    imagemagick \
    libmagickwand-dev \
    ghostscript \
    libgs-dev \
    && rm -rf /var/lib/apt/lists/* \
    && sed -iE '/^listen/s/\/run\/php\/php7\.3-fpm\.sock/9000/g' /etc/php/7.3/fpm/pool.d/www.conf \
    && mkdir -p /var/www/html \
    && mkdir -p /run/php \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN sed -i -e 's/<policy domain="coder" rights="none" pattern="PDF" \/>/<policy domain="coder" rights="read|write" pattern="PDF" \/>/' /etc/ImageMagick-6/policy.xml

ADD upload.ini /etc/php/7.3/cli/conf.d/
ADD upload.ini /etc/php/7.3/fpm/conf.d/

ENV COMPOSER_ALLOW_SUPERUSER 1
WORKDIR /var/www/html
RUN chown www-data:www-data -R /var/www/html
EXPOSE 9000

CMD [ "/usr/sbin/php-fpm7.3", "-F" ]
