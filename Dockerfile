# استخدام صورة PHP مع Apache
FROM php:8.2-apache

# تثبيت الامتدادات المطلوبة لـ MongoDB و cURL و zip و git
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install curl zip

# تثبيت Composer بأمان
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# تمكين امتداد MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# ضبط Apache ليعمل مع المشروع
COPY . /var/www/html/
WORKDIR /var/www/html/

# تثبيت الاعتماديات باستخدام Composer
RUN composer install --no-dev --prefer-dist --optimize-autoloader --ignore-platform-reqs

# ضبط التصاريح للمجلدات
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
RUN chown -R www-data:www-data /var/www/html/vendor && chmod -R 755 /var/www/html/vendor

# تعيين المنفذ الافتراضي لـ Apache
EXPOSE 80

# تشغيل Apache عند بدء التشغيل
CMD ["apache2-foreground"]
