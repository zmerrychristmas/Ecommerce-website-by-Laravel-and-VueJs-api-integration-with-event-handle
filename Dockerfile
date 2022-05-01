FROM php:8.0-fpm
 
# Copy composer.lock and composer.json into the working directory
COPY composer.lock composer.json /var/www/html/
 
# Set working directory
WORKDIR /var/www/html/
 
# Install dependencies for the operating system software
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    libonig-dev \
    curl \
    build-essential \
    openssl \
    libssl-dev curl
 
RUN groupadd dev
# node install

ENV NODE_VERSION=15.0.0
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/usr/local/nvm/.nvm
RUN mkdir /usr/local/nvm
RUN mkdir /usr/local/node
RUN chown -R root:dev /usr/local/nvm
RUN chmod -R 775 /usr/local/nvm
RUN chown -R root:dev /usr/local/node
RUN chmod -R 775 /usr/local/node
RUN cp -R /root/.nvm/ /usr/local/nvm/
ENV NVM_DIR=/usr/local/nvm/.nvm
RUN export NVM_DIR=/usr/local/nvm/.nvm
RUN . "/usr/local/nvm/.nvm/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "/usr/local/nvm/.nvm/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "/usr/local/nvm/.nvm/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/usr/local/nvm/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version



# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
 
# Install extensions for php
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
 
# Install composer (php package manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
 
# Copy existing application directory contents to the working directory
COPY . /var/www/html
 
RUN composer install
RUN composer dump-autoload
RUN chmod 777 install.sh
CMD ["./install.sh"]

# Assign permissions of the working directory to the www-data user
RUN chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache
RUN chmod -R 777 /var/www/html/storage
# Expose port 9000 and start php-fpm server (for FastCGI Process Manager)
EXPOSE 9000
CMD ["php-fpm"]