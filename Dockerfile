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

# Installer dépendances Laravel (sans script pour éviter crash)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Générer key Laravel (important)
RUN php artisan key:generate || true

# Permissions finales
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80