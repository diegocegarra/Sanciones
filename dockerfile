# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos dependencias del sistema y extensiones necesarias para bases de datos
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpiamos caché para reducir el peso de la imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalamos extensiones de PHP (ej. pdo_mysql para conectar a MySQL)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Activamos mod_rewrite de Apache para URLs amigables (ej. Laravel)
RUN a2enmod rewrite

# Copiamos Composer desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definimos el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Cambiamos la ruta por defecto de Apache al directorio public de Laravel (si aplica)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copiamos el código del proyecto al contenedor
COPY . /var/www/html

# Damos permisos correctos a las carpetas de almacenamiento (ej. Laravel)
# Creamos las carpetas si no existen y damos permisos a Apache
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    
# Exponemos el puerto 80 para la web
EXPOSE 80