FROM rknol/php7-symfony
MAINTAINER Ruben Knol <c.minor6@gmail.com>

# Copy the project into the directory that php-fpm
# serves files from
COPY . /srv/symfony

# Set file permissions so that
RUN chmod -R 777 /srv/symfony

# Install library dependencies with Composer
RUN composer install