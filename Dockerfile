FROM php:8.2-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring xml

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activer mod_rewrite (important Laravel)
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html

WORKDIR /var/www/html

# Créer les dossiers nécessaires AVANT composer
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Créer le fichier .env s'il n'existe pas
RUN cp .env.example .env || echo "APP_NAME=IdentiGuinee\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=identiguinee\nDB_USERNAME=root\nDB_PASSWORD=\n" > .env

# Installer dépendances Laravel avec options compatibles
RUN composer install --no-interaction --no-scripts --prefer-dist --optimize-autoloader

# Générer la clé Laravel
RUN php artisan key:generate --force

# Configurer Apache pour Laravel
RUN echo '<VirtualHost *:80>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n    </Directory>\n    ErrorLog ${APACHE_LOG_DIR}/error.log\n    CustomLog ${APACHE_LOG_DIR}/access.log combined\n</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Désactiver le site par défaut et activer le nôtre
RUN a2dissite 000-default.conf && a2ensite 000-default.conf

# Permissions finales
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80