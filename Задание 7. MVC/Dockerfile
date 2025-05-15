FROM php:8.2-apache

# Устанавливаем системные зависимости и PHP расширения
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем весь проект
COPY . .

# Устанавливаем зависимости (включая twig и fawno/fpdf)
RUN composer install || composer update
RUN composer require mpdf/mpdf
RUN composer require dompdf/dompdf
# Включаем mod_rewrite
RUN a2enmod rewrite

# Настраиваем Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Открываем порт
EXPOSE 80

# Запускаем Apache
CMD ["apache2-foreground"]
