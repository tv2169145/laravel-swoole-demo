FROM php:7.4-fpm
WORKDIR "/app"

# Fix debconf warnings upon build &  Install GD extension
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends apt-utils
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Update packages & Install Git and Supervisor
RUN apt-get update -y && \
    apt-get install -yq git supervisor

# Install GD extension
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg
# RUN docker-php-ext-install -j "$(nproc)" gd

# Install Swoole extension
RUN pecl install swoole
RUN docker-php-ext-enable swoole

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Added supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisor.conf
CMD ["/usr/bin/supervisord", "-n"]
