# استخدم صورة PHP مع Apache
FROM php:8.2-apache

# تثبيت الامتدادات المطلوبة لـ MongoDB و cURL
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# تمكين امتداد MongoDB
RUN pecl install mongodb && docker-php-ext-enable mongodb

# ضبط Apache ليعمل مع المشروع
COPY . /var/www/html/
WORKDIR /var/www/html/

# تثبيت الاعتماديات باستخدام Composer
RUN composer install

# ضبط التصاريح للمجلدات
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# تعيين المنفذ الافتراضي لـ Apache
EXPOSE 80

# تشغيل Apache عند بدء التشغيل
CMD ["apache2-foreground"]
