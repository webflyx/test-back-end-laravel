FROM php:8.3-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    npm

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN apt-get install -y nodejs

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd opcache

###
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql pgsql
RUN pecl install redis
RUN docker-php-ext-enable redis


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./ /var/www/abz-agency.loc

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer
RUN chown -R $user:$user /home/$user
RUN chown -R $user:$user /var/www
RUN chmod 755 /var/www/abz-agency.loc/package.json


# Zend Opcache Settings
RUN echo '\
opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=32\n\
opcache.max_accelerated_files=80000\n\
opcache.revalidate_freq=3\n\
opcache.fast_shutdown=1\n\
opcache.enable_cli=1\n\
opcache.jit_buffer_size=128m\n\
opcache.jit=1205\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Set working directory
WORKDIR /var/www/abz-agency.loc

# Change active user
USER $user

# Build css, js files
#RUN npm i

# Composer autoload
RUN composer install


# php artisa migrate --seed
# php artisan passport:install
# npm run build
