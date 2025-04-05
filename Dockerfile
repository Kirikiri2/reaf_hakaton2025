FROM php:8.1-apache

# Установка необходимых PHP расширений
RUN docker-php-ext-install pdo pdo_mysql

# Включение mod_rewrite
RUN a2enmod rewrite

# Копирование конфигурации Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Настройка кодировки
RUN echo "default_charset = UTF-8" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.internal_encoding = UTF-8" >> /usr/local/etc/php/php.ini \
    && echo "mbstring.http_output = UTF-8" >> /usr/local/etc/php/php.ini

# Установка рабочей директории
WORKDIR /var/www/html

# Копирование файлов проекта
COPY . /var/www/html/

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Создание директории для загрузок
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 777 /var/www/html/uploads

# Открытие порта
EXPOSE 80 