FROM dunglas/frankenphp

# Répertoire de travail
WORKDIR /app

# Copie du projet
COPY . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo_mysql

# Installation des dépendances PHP
RUN composer install --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /app
USER www-data
