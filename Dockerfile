# استخدم صورة PHP مع Apache
FROM php:8.2-apache

# تثبيت الأدوات والمكتبات المطلوبة للـ Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    default-mysql-client

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع إلى مجلد Apache
COPY . /var/www/html

# تعيين صلاحيات الملفات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# تفعيل mod_rewrite للـ Apache
RUN a2enmod rewrite

# تعيين مجلد العمل
WORKDIR /var/www/html

# تشغيل Laravel server (يمكن تغييره لاحقاً)
CMD ["apache2-foreground"]
