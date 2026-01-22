# Imagen base PHP 8.4 CLI
FROM php:8.4-cli

# Establecer directorio de trabajo
WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    opcache

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos del proyecto
COPY . /app

# Instalar dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader

# Crear directorios necesarios
RUN mkdir -p var/log var/cache && chmod -R 777 var/

# Exponer puerto
EXPOSE 8000

# Comando por defecto: iniciar servidor PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
