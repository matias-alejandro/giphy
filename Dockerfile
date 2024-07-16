FROM php:8.2-apache
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY src .

EXPOSE 80

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD [ "bash /usr/local/bin/entrypoint.sh" ]

