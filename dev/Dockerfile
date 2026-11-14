# Local dev only: PHP 8.3 + Apache with GD, mod_rewrite, headers, expires,
# close to a typical cPanel/Hostinger setup.
FROM php:8.3-apache
RUN apt-get update \
 && apt-get install -y --no-install-recommends libjpeg62-turbo-dev libpng-dev libwebp-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
 && docker-php-ext-install gd \
 && a2enmod rewrite headers expires deflate \
 && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && rm -rf /var/lib/apt/lists/*
RUN { echo 'upload_max_filesize=16M'; echo 'post_max_size=80M'; echo 'max_file_uploads=30'; echo 'memory_limit=256M'; } > /usr/local/etc/php/conf.d/uploads.ini
